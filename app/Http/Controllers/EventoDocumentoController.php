<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Documento;
use Illuminate\Http\Request;

class EventoDocumentoController extends Controller
{
    public function store(Request $request, $evento)
    {
        $evento = Evento::findOrFail($evento);

        $request->validate([
            'nome_file' => 'required|string|max:255',
            'tipologia' => 'required|in:documento,programma,locandina,altro',
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('documenti/eventi', 'public');

        $documento = Documento::create([
            'nome_file' => $request->nome_file,
            'tipologia' => $request->tipologia,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'dimensione' => $file->getSize(),
            'visibilita' => 'evento',
            'visibilita_target_id' => $evento->id,
            'visibilita_target_type' => Evento::class,
        ]);

        $evento->documenti()->attach($documento->id);

        return redirect()->back()->with('success', 'Documento caricato con successo.');
    }

    public function destroy($evento, $documento)
    {
        $evento = Evento::findOrFail($evento);
        $documento = Documento::findOrFail($documento);

        if ($evento->documenti()->where('documenti.id', $documento->id)->exists()) {
            $evento->documenti()->detach($documento->id);
        }

        if ($documento->visibilita === 'evento' && $documento->visibilita_target_id == $evento->id) {
            if ($documento->eventi()->count() === 0 && $documento->gruppi()->count() === 0 && $documento->individui()->count() === 0) {
                if ($documento->file_path && \Storage::disk('public')->exists($documento->file_path)) {
                    \Storage::disk('public')->delete($documento->file_path);
                }
                $documento->delete();
            }
        }

        return redirect()->back()->with('success', 'Documento rimosso.');
    }
}