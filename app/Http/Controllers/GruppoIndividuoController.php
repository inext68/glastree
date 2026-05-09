<?php

namespace App\Http\Controllers;

use App\Models\Gruppo;
use Illuminate\Http\Request;

class GruppoIndividuoController extends Controller
{
    public function store(Request $request, $individuo)
    {
        $data = $request->validate([
            'gruppo_id' => 'required|exists:gruppi,id',
            'ruolo_nel_gruppo' => 'nullable|string|max:100',
            'data_adesione' => 'nullable|date',
        ]);

        $individuo = \App\Models\Individuo::findOrFail($individuo);
        $gruppo = Gruppo::findOrFail($data['gruppo_id']);

        if ($individuo->gruppi->contains($gruppo->id)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Già associato a questo gruppo.'], 422);
            }
            return redirect()->back()->with('error', 'Già associato a questo gruppo.');
        }

        $individuo->gruppi()->attach($gruppo->id, [
            'ruolo_nel_gruppo' => $data['ruolo_nel_gruppo'] ?? null,
            'data_adesione' => $data['data_adesione'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Gruppo associato.']);
        }
        return redirect()->back()->with('success', 'Gruppo associato.');
    }

    public function update(Request $request, $individuo, $gruppo)
    {
        $data = $request->validate([
            'ruolo_nel_gruppo' => 'nullable|string|max:100',
            'data_adesione' => 'nullable|date',
        ]);

        $individuo = \App\Models\Individuo::findOrFail($individuo);
        $gruppoModel = Gruppo::findOrFail($gruppo);

        $individuo->gruppi()->updateExistingPivot($gruppoModel->id, [
            'ruolo_nel_gruppo' => $data['ruolo_nel_gruppo'] ?? null,
            'data_adesione' => $data['data_adesione'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Associazione aggiornata.');
    }

    public function destroy($individuo, $gruppo)
    {
        $individuo = \App\Models\Individuo::findOrFail($individuo);
        $individuo->gruppi()->detach($gruppo);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json(['success' => 'Rimosso dal gruppo.']);
        }
        return redirect()->back()->with('success', 'Rimosso dal gruppo.');
    }
}