<?php

namespace App\Http\Controllers;

use App\Models\VistaReport;
use Illuminate\Http\Request;

class VistaReportController extends Controller
{
    public function index()
    {
        $viste = VistaReport::where('user_id', auth()->id())
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get();
        
        return view('viste.index', compact('viste'));
    }

    public function showJson(VistaReport $vistaReport)
    {
        if ($vistaReport->user_id !== auth()->id()) {
            abort(403);
        }
        
        return response()->json($vistaReport);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:individui,gruppi,documenti,eventi',
            'colonne_visibili' => 'nullable|array',
            'colonne_ordinamento' => 'nullable|array',
            'filtri' => 'nullable|array',
            'ricerca' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = filter_var($data['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;

        if ($isDefault) {
            VistaReport::where('user_id', auth()->id())
                ->where('tipo', $data['tipo'])
                ->update(['is_default' => false]);
        }

        $vista = VistaReport::create([
            'user_id' => auth()->id(),
            'nome' => $data['nome'],
            'tipo' => $data['tipo'],
            'colonne_visibili' => $data['colonne_visibili'] ?? null,
            'colonne_ordinamento' => $data['colonne_ordinamento'] ?? null,
            'filtri' => $data['filtri'] ?? null,
            'ricerca' => $data['ricerca'] ?? null,
            'is_default' => $isDefault,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Vista salvata.']);
        }

        return back()->with('success', 'Vista salvata.');
    }

    public function update(Request $request, VistaReport $vistaReport)
    {
        if ($vistaReport->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'colonne_visibili' => 'nullable|array',
            'colonne_ordinamento' => 'nullable|array',
            'filtri' => 'nullable|array',
            'ricerca' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($data['is_default'])) {
            VistaReport::where('user_id', auth()->id())
                ->where('tipo', $vistaReport->tipo)
                ->where('id', '!=', $vistaReport->id)
                ->update(['is_default' => false]);
        }

        $vistaReport->update($data);

        return back()->with('success', 'Vista aggiornata.');
    }

    public function setDefault(Request $request, VistaReport $vistaReport)
    {
        if ($vistaReport->user_id !== auth()->id()) {
            abort(403);
        }

        VistaReport::where('user_id', auth()->id())
            ->where('tipo', $vistaReport->tipo)
            ->update(['is_default' => false]);

        $vistaReport->update(['is_default' => true]);

        return back()->with('success', 'Vista predefinita impostata.');
    }

    public static function getDefaultForUser($tipo)
    {
        return VistaReport::where('user_id', auth()->id())
            ->where('tipo', $tipo)
            ->where('is_default', true)
            ->first();
    }

    public function destroy(VistaReport $vistaReport)
    {
        if ($vistaReport->user_id !== auth()->id()) {
            abort(403);
        }

        $vistaReport->delete();

        return back()->with('success', 'Vista eliminata.');
    }
}