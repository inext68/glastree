<?php

namespace App\Http\Controllers;

use App\Models\MailingList;
use Illuminate\Http\Request;

class MailingListController extends Controller
{
    public function index()
    {
        $mailingLists = MailingList::with(['user', 'contatti'])
            ->orderBy('nome')
            ->get();
        return view('mailing-liste.index', compact('mailingLists'));
    }

    public function create()
    {
        return view('mailing-liste.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'attiva' => 'boolean',
            'contatti_json' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['attiva'] = $data['attiva'] ?? true;

        $lista = MailingList::create([
            'nome' => $data['nome'],
            'descrizione' => $data['descrizione'] ?? null,
            'attiva' => $data['attiva'],
            'user_id' => $data['user_id'],
        ]);

        if (!empty($data['contatti_json'])) {
            $contatti = json_decode($data['contatti_json'], true);
            if (is_array($contatti)) {
                foreach ($contatti as $contatto) {
                    if (!empty($contatto['individuo_id']) && !empty($contatto['email'])) {
                        $lista->contatti()->create([
                            'individuo_id' => $contatto['individuo_id'],
                            'opt_in' => true,
                            'opt_in_data' => now(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('mailing-liste.index')->with('success', 'Lista creata con ' . $lista->contatti()->count() . ' contatti.');
    }

    public function show($mailing_liste)
    {
        $mailingList = MailingList::with(['contatti.individuo.contatti'])->findOrFail($mailing_liste);
        return view('mailing-liste.show', compact('mailingList'));
    }

    public function edit($mailingList)
    {
        $mailingList = MailingList::with('contatti.individuo.contatti')->findOrFail($mailingList);
        return view('mailing-liste.edit', compact('mailingList'));
    }

    public function update(Request $request, $mailingList)
    {
        $mailingList = MailingList::findOrFail($mailingList);
        
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'attiva' => 'boolean',
            'contatti_json' => 'nullable|string',
        ]);

        $data['attiva'] = $data['attiva'] ?? false;

        $mailingList->update([
            'nome' => $data['nome'],
            'descrizione' => $data['descrizione'] ?? null,
            'attiva' => $data['attiva'],
        ]);

        if (!empty($data['contatti_json'])) {
            $newContatti = json_decode($data['contatti_json'], true);
            if (is_array($newContatti)) {
                $existingIds = $mailingList->contatti()->pluck('individuo_id')->toArray();
                $newIds = array_column($newContatti, 'individuo_id');
                
                $toRemove = array_diff($existingIds, $newIds);
                if (!empty($toRemove)) {
                    $mailingList->contatti()->whereIn('individuo_id', $toRemove)->delete();
                }
                
                $toAdd = array_diff($newIds, $existingIds);
                foreach ($newContatti as $contatto) {
                    if (in_array($contatto['individuo_id'], $toAdd) && !empty($contatto['email'])) {
                        $mailingList->contatti()->create([
                            'individuo_id' => $contatto['individuo_id'],
                            'opt_in' => true,
                            'opt_in_data' => now(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('mailing-liste.show', $mailingList->id)->with('success', 'Lista aggiornata.');
    }

    public function destroy($mailingList)
    {
        $mailingList = MailingList::findOrFail($mailingList);
        $mailingList->delete();
        return redirect()->route('mailing-liste.index')->with('success', 'Lista eliminata.');
    }

    public function createFromIndividui(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'contatti' => 'required|array',
            'contatti.*.individuo_id' => 'required|exists:individui,id',
            'contatti.*.email' => 'required|email',
        ]);

        $lista = MailingList::create([
            'nome' => $data['nome'],
            'descrizione' => $data['descrizione'] ?? null,
            'attiva' => true,
            'user_id' => auth()->id(),
        ]);

        foreach ($data['contatti'] as $contatto) {
            $lista->contatti()->create([
                'individuo_id' => $contatto['individuo_id'],
                'opt_in' => true,
                'opt_in_data' => now(),
            ]);
        }

        return response()->json(['success' => true, 'lista_id' => $lista->id]);
    }
}