<?php

namespace App\Http\Controllers;

use App\Models\Gruppo;
use App\Models\Individuo;
use Illuminate\Http\Request;

class GruppoMembroController extends Controller
{
    public function store(Request $request, $gruppo)
    {
        $gruppo = Gruppo::findOrFail($gruppo);
        
        $data = $request->validate([
            'individuo_id' => 'required|exists:individui,id',
            'ruolo_nel_gruppo' => 'nullable|string|max:255',
            'data_adesione' => 'nullable|date',
        ]);

        if ($gruppo->individui()->where('individuo_id', $data['individuo_id'])->exists()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Questo individuo è già membro di questo gruppo.'], 422);
            }
            return redirect()->back()->with('error', 'Questo individuo è già membro di questo gruppo.');
        }

        $gruppo->individui()->attach($data['individuo_id'], [
            'ruolo_nel_gruppo' => $data['ruolo_nel_gruppo'] ?? null,
            'data_adesione' => $data['data_adesione'] ?? null,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => 'Membro aggiunto con successo.']);
        }
        return redirect()->back()->with('success', 'Membro aggiunto con successo.');
    }

    public function update(Request $request, $gruppo, $individuo)
    {
        $gruppo = Gruppo::findOrFail($gruppo);
        
        $data = $request->validate([
            'ruolo_nel_gruppo' => 'nullable|string|max:255',
            'data_adesione' => 'nullable|date',
        ]);

        $gruppo->individui()->updateExistingPivot($individuo, [
            'ruolo_nel_gruppo' => $data['ruolo_nel_gruppo'] ?? null,
            'data_adesione' => $data['data_adesione'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Membro aggiornato con successo.');
    }

    public function destroy($gruppo, $individuo)
    {
        $gruppo = Gruppo::findOrFail($gruppo);
        $gruppo->individui()->detach($individuo);
        
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json(['success' => 'Membro rimosso dal gruppo.']);
        }
        return redirect()->back()->with('success', 'Membro rimosso dal gruppo.');
    }
}