<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Individuo;
use App\Models\Gruppo;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $documenti = Documento::with(['user', 'target'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $individui = Individuo::orderBy('cognome')->orderBy('nome')->get();
        $gruppi = Gruppo::orderBy('nome')->get();
        $eventi = Evento::orderBy('nome_evento')->get();

        return view('documenti.index', compact('documenti', 'individui', 'gruppi', 'eventi'));
    }

    public function edit($documento)
    {
        $documento = Documento::with('target')->findOrFail($documento);
        return view('documenti.edit', compact('documento'));
    }

    public function update(Request $request, $documento)
    {
        $documento = Documento::findOrFail($documento);

        $data = $request->validate([
            'nome_file' => 'required|string|max:255',
            'tipologia' => 'required|in:avatar,galleria,documento,statuto,altro',
            'visibilita' => 'required|in:pubblico,individuo,gruppo,associazione,federazione',
            'visibilita_target_id' => 'nullable|integer',
            'visibilita_target_type' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $documento->update($data);

        return redirect('/documenti')->with('success', 'Documento aggiornato.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_file' => 'required|string|max:255',
            'tipologia' => 'required|in:avatar,galleria,documento,statuto,altro',
            'file' => 'required|file|max:10240',
            'visibilita' => 'required|in:pubblico,individuo,gruppo',
            'visibilita_target_id' => 'nullable|integer',
            'visibilita_target_type' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documenti', 'local');

        Documento::create([
            'nome_file' => $data['nome_file'],
            'file_path' => $path,
            'tipo' => 'upload',
            'tipologia' => $data['tipologia'],
            'visibilita' => $data['visibilita'],
            'visibilita_target_id' => $data['visibilita_target_id'] ?? null,
            'visibilita_target_type' => $data['visibilita_target_type'] ?? null,
            'mime_type' => $file->getMimeType(),
            'dimensione' => $file->getSize(),
            'user_id' => auth()->id(),
        ]);

        $redirect = $request->_redirect ?? url()->previous();
        return redirect($redirect)->with('success', 'Documento caricato.');
    }

    public function download($documento)
    {
        $documento = Documento::findOrFail($documento);

        if (!Storage::disk('local')->exists($documento->file_path)) {
            abort(404);
        }

        $extension = pathinfo($documento->file_path, PATHINFO_EXTENSION);
        $filename = $extension ? $documento->nome_file . '.' . $extension : $documento->nome_file;

        return Storage::disk('local')->download(
            $documento->file_path,
            $filename,
            ['Content-Type' => $documento->mime_type]
        );
    }

    public function preview($documento)
    {
        $documento = Documento::findOrFail($documento);

        if (!Storage::disk('local')->exists($documento->file_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('local')->path($documento->file_path);
        $mimeType = $documento->mime_type;

        if (str_starts_with($mimeType, 'image/')) {
            return response()->file($fullPath, ['Content-Type' => $mimeType]);
        }

        return response()->file($fullPath, ['Content-Type' => $mimeType]);
    }

    public function destroy($documento)
    {
        $documento = Documento::findOrFail($documento);

        if (Storage::disk('local')->exists($documento->file_path)) {
            Storage::disk('local')->delete($documento->file_path);
        }

        $documento->delete();

        $redirect = request('_redirect', url('/'));
        return redirect($redirect)->with('success', 'Documento eliminato.');
    }

    public function massDestroy(Request $request)
    {
        $idsInput = $request->input('ids', '');
        $ids = is_array($idsInput) ? $idsInput : (is_string($idsInput) ? explode(',', $idsInput) : []);
        
        if (empty($ids)) {
            return back()->with('error', 'Nessun documento selezionato.');
        }

        $documenti = Documento::whereIn('id', $ids)->get();
        
        foreach ($documenti as $documento) {
            if (Storage::disk('local')->exists($documento->file_path)) {
                Storage::disk('local')->delete($documento->file_path);
            }
            $documento->delete();
        }

        return back()->with('success', count($ids) . ' documenti eliminati.');
    }

    public function massUpdate(Request $request)
    {
        $idsInput = $request->input('ids', '');
        $ids = is_array($idsInput) ? $idsInput : (is_string($idsInput) ? explode(',', $idsInput) : []);
        
        if (empty($ids)) {
            return back()->with('error', 'Nessun documento selezionato.');
        }

        $data = $request->validate([
            'tipologia' => 'nullable|in:avatar,galleria,documento,statuto,altro',
            'visibilita' => 'nullable|in:pubblico,individuo,gruppo,associazione,federazione',
        ]);

        if (empty($data['tipologia']) && empty($data['visibilita'])) {
            return back()->with('error', 'Specificare almeno un campo da aggiornare.');
        }

        Documento::whereIn('id', $ids)->update($data);

        return back()->with('success', count($ids) . ' documenti aggiornati.');
    }

    public function massAssociate(Request $request)
    {
        $idsInput = $request->input('ids', '');
        $ids = is_array($idsInput) ? $idsInput : (is_string($idsInput) ? explode(',', $idsInput) : []);
        
        if (empty($ids)) {
            return back()->with('error', 'Nessun documento selezionato.');
        }

        $data = $request->validate([
            'target_type' => 'required|in:individuo,gruppo,evento',
            'target_id' => 'required|integer',
        ]);

        $targetType = match($data['target_type']) {
            'individuo' => Individuo::class,
            'gruppo' => Gruppo::class,
            'evento' => Evento::class,
        };

        Documento::whereIn('id', $ids)->update([
            'visibilita' => $data['target_type'],
            'visibilita_target_id' => $data['target_id'],
            'visibilita_target_type' => $targetType,
        ]);

        return back()->with('success', count($ids) . ' documenti associati.');
    }
}