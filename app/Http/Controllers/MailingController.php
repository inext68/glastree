<?php

namespace App\Http\Controllers;

use App\Models\MailingList;
use App\Models\MailingContact;
use App\Models\Individuo;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MailingController extends Controller
{
    public function nuovo(Request $request)
    {
        $individui = collect();
        $individuiIds = $request->input('individui');

        if ($individuiIds) {
            $ids = array_map('trim', explode(',', $individuiIds));
            $individui = Individuo::with('contatti')->whereIn('id', $ids)->get();
        }

        $liste = MailingList::all();
        $documenti = Documento::orderBy('nome_file')->get();

        $documentiSelezionati = collect();
        if ($request->has('documenti_selezionati')) {
            $docIds = array_filter(array_map('trim', explode(',', $request->input('documenti_selezionati'))));
            $documentiSelezionati = Documento::whereIn('id', $docIds)->get();
        }

        return view('mailing.nuovo', compact('individui', 'liste', 'documenti', 'documentiSelezionati'));
    }

    public function invia(Request $request)
    {
        $data = $request->validate([
            'oggetto' => 'required|string|max:255',
            'corpo' => 'required',
            'lista_id' => 'nullable|exists:mailing_liste,id',
            'destinatari_ids' => 'nullable|string',
            'documenti_selezionati' => 'nullable|string',
            'allegato' => 'nullable|file|max:10240',
        ]);

        $documentiAllegati = [];

        if ($request->hasFile('allegato')) {
            $file = $request->file('allegato');
            $nomeOriginale = $file->getClientOriginalName();
            $path = $file->store('documenti/allegati');

            $documento = Documento::create([
                'nome_file' => $nomeOriginale,
                'file_path' => $path,
                'tipo' => 'allegato',
                'tipologia' => 'allegato_email',
                'visibilita' => 'pubblico',
                'dimensione' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => auth()->id(),
            ]);

            $documentiAllegati[] = $documento->id;
        }

        if (!empty($data['documenti_selezionati'])) {
            $docIds = array_filter(array_map('trim', explode(',', $data['documenti_selezionati'])));
            $documentiAllegati = array_merge($documentiAllegati, $docIds);
        }

        $destinatari = [];
        if (!empty($data['destinatari_ids'])) {
            $ids = array_filter(array_map('trim', explode(',', $data['destinatari_ids'])));
            $destinatari = Individuo::with('contatti')->whereIn('id', $ids)->get();
        }

        return back()->with('success', 'Messaggio preparato per ' . count($destinatari) . ' destinatari con ' . count($documentiAllegati) . ' allegato(i).');
    }

    public function invio(Request $request)
    {
        $liste = MailingList::where('attiva', true)->orderBy('nome')->get();
        $documenti = Documento::orderBy('nome_file')->get();

        return view('mailing.invio', compact('liste', 'documenti'));
    }

    public function invioElabora(Request $request)
    {
        $data = $request->validate([
            'liste' => 'required|array',
            'liste.*' => 'exists:mailing_liste,id',
            'oggetto' => 'required|string|max:255',
            'corpo' => 'required',
            'documenti_selezionati' => 'nullable|array',
            'documenti_selezionati.*' => 'exists:documenti,id',
            'allegato' => 'nullable|file|max:10240',
        ]);

        $documentiAllegati = [];

        if ($request->hasFile('allegato')) {
            $file = $request->file('allegato');
            $nomeOriginale = $file->getClientOriginalName();
            $path = $file->store('documenti/allegati');

            $documento = Documento::create([
                'nome_file' => $nomeOriginale,
                'file_path' => $path,
                'tipo' => 'allegato',
                'tipologia' => 'allegato_email',
                'visibilita' => 'pubblico',
                'dimensione' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => auth()->id(),
            ]);

            $documentiAllegati[] = $documento->id;
        }

        if (!empty($data['documenti_selezionati'])) {
            $documentiAllegati = array_merge($documentiAllegati, $data['documenti_selezionati']);
        }

        $contatti = MailingContact::whereIn('mailing_list_id', $data['liste'])
            ->where('opt_in', true)
            ->with('individuo.contatti')
            ->get();

        $destinatari = $contatti->map(function($c) {
            return $c->individuo->contatti->where('tipo', 'email')->first()?->valore;
        })->filter()->unique()->values();

        return back()->with('success', 'Messaggio pronto per l\'invio a ' . $destinatari->count() . ' destinatari unici da ' . count($data['liste']) . ' lista(e).');
    }
}