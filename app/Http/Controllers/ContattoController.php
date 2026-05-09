<?php

namespace App\Http\Controllers;

use App\Models\Contatto;
use Illuminate\Http\Request;

class ContattoController extends Controller
{
    public function update($contatto)
    {
        $contatto = Contatto::findOrFail($contatto);
        $data = request()->validate([
            'tipo' => 'required|in:telefono,cellulare,email,fax,web,telegram,whatsapp,altro',
            'valore' => 'required|string|max:255',
            'etichetta' => 'nullable|string|max:100',
            'is_primary' => 'nullable',
        ]);

        $contatto->update([
            'tipo' => $data['tipo'],
            'valore' => $data['valore'],
            'etichetta' => $data['etichetta'] ?? null,
            'is_primary' => isset($data['is_primary']),
        ]);

        $redirect = request('_redirect', route('individui.show', $contatto->individuo_id));
        return redirect($redirect)->with('success', 'Contatto aggiornato.');
    }

    public function destroy($contatto)
    {
        $contatto = Contatto::findOrFail($contatto);
        $individuo_id = $contatto->individuo_id;
        $contatto->delete();

        $redirect = request('_redirect', route('individui.show', $individuo_id));
        return redirect($redirect)->with('success', 'Contatto eliminato.');
    }
}