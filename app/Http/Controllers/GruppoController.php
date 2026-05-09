<?php

namespace App\Http\Controllers;

use App\Models\Gruppo;
use App\Models\Individuo;
use App\Models\Diocesi;
use Illuminate\Http\Request;

class GruppoController extends Controller
{
    public function index()
    {
        $flatGruppi = Gruppo::with(['parent', 'diocesi', 'responsabile'])
            ->withCount('individui')
            ->orderBy('nome')
            ->get()
            ->map(function ($gruppo) {
                $depth = 0;
                $parent = $gruppo->parent;
                while ($parent) {
                    $depth++;
                    $parent = $parent->parent;
                }
                $gruppo->depth = $depth;
                $gruppo->children_count = $gruppo->children()->count();
                return $gruppo;
            });

        $rootGruppi = $flatGruppi->filter(fn($g) => $g->parent_id === null)->values();
        $childGruppi = $flatGruppi->filter(fn($g) => $g->parent_id !== null)->values();

        $rootGruppi = Gruppo::whereNull('parent_id')
            ->with(['diocesi', 'responsabile', 'children.diocesi', 'children.responsabile', 'children.children.diocesi', 'children.children.responsabile'])
            ->withCount('individui')
            ->orderBy('nome')
            ->get()
            ->map(function ($gruppo) {
                $gruppo->depth = 0;
                $gruppo->individui_count = $gruppo->individui_count ?? $gruppo->individui()->count();
                return $gruppo;
            });

        return view('gruppi.index', compact('rootGruppi', 'childGruppi', 'flatGruppi'));
    }

    public function create(Request $request)
    {
        $diocesi = Diocesi::orderBy('nome')->get();
        $allGruppi = Gruppo::orderBy('nome')->get();
        $selectedParent = $request->query('parent_id') ? Gruppo::find($request->query('parent_id')) : null;
        $individui = Individuo::orderBy('cognome')->orderBy('nome')->get();
        return view('gruppi.create', compact('diocesi', 'allGruppi', 'selectedParent', 'individui'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'parent_id' => 'nullable|exists:gruppi,id',
            'diocesi_id' => 'nullable|exists:diocesi,id',
            'responsabile_id' => ['nullable', function ($attribute, $value, $fail) {
                if ($value !== '' && $value !== null && !\App\Models\Individuo::where('id', $value)->exists()) {
                    $fail('Il responsabile selezionato non esiste.');
                }
            }],
            'indirizzo_incontro' => 'nullable|string|max:500',
            'cap_incontro' => 'nullable|string|max:10',
            'città_incontro' => 'nullable|string|max:255',
            'sigla_provincia_incontro' => 'nullable|string|max:2',
            'mappa_posizione' => 'nullable|string',
        ]);

        $gruppo = Gruppo::create($data);

        if ($request->has('individui')) {
            foreach ($request->individui as $individuoData) {
                if (!empty($individuoData['individuo_id'])) {
                    $gruppo->individui()->attach($individuoData['individuo_id'], [
                        'ruolo_nel_gruppo' => $individuoData['ruolo_nel_gruppo'] ?? null,
                        'data_adesione' => $individuoData['data_adesione'] ?? null,
                    ]);
                }
            }
        }

        return redirect('/gruppi/' . $gruppo->id)->with('success', 'Gruppo creato con successo.');
    }

    public function show($gruppo)
    {
        $gruppo = Gruppo::with(['parent', 'children', 'diocesi', 'responsabile', 'individui.contatti', 'documenti', 'eventi' => function ($q) {
            $q->where('is_incontro_gruppo', true);
        }, 'eventi.responsabili.contatti'])->findOrFail($gruppo);
        return view('gruppi.show', compact('gruppo'));
    }

    public function edit($gruppo)
    {
        $gruppo = Gruppo::with(['individui.contatti', 'individui.documenti'])->findOrFail($gruppo);
        $diocesi = Diocesi::orderBy('nome')->get();
        $gruppi = Gruppo::where('id', '!=', $gruppo->id)->orderBy('nome')->get();
        $membri = $gruppo->individui()->orderBy('cognome')->orderBy('nome')->get();
        return view('gruppi.edit', compact('gruppo', 'diocesi', 'gruppi', 'membri'));
    }

    public function update(Request $request, $gruppo)
    {
        $gruppo = Gruppo::findOrFail($gruppo);
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'parent_id' => 'nullable|exists:gruppi,id',
            'diocesi_id' => 'nullable|exists:diocesi,id',
            'responsabile_id' => ['nullable', function ($attribute, $value, $fail) {
                if ($value !== '' && $value !== null && !\App\Models\Individuo::where('id', $value)->exists()) {
                    $fail('Il responsabile selezionato non esiste.');
                }
            }],
            'indirizzo_incontro' => 'nullable|string|max:500',
            'cap_incontro' => 'nullable|string|max:10',
            'città_incontro' => 'nullable|string|max:255',
            'sigla_provincia_incontro' => 'nullable|string|max:2',
            'mappa_posizione' => 'nullable|string',
        ]);

        $gruppo->update($data);

        if ($request->has('individui')) {
            $gruppo->individui()->detach();
            foreach ($request->individui as $individuoData) {
                if (!empty($individuoData['individuo_id'])) {
                    $gruppo->individui()->attach($individuoData['individuo_id'], [
                        'ruolo_nel_gruppo' => $individuoData['ruolo_nel_gruppo'] ?? null,
                        'data_adesione' => $individuoData['data_adesione'] ?? null,
                    ]);
                }
            }
        }

        return redirect('/gruppi/' . $gruppo->id)->with('success', 'Gruppo aggiornato.');
    }

    public function destroy($gruppo)
    {
        $gruppo = Gruppo::findOrFail($gruppo);
        foreach ($gruppo->children as $child) {
            $this->destroy($child);
        }
        $gruppo->delete();
        return redirect('/gruppi')->with('success', 'Gruppo eliminato.');
    }

    public function allGruppi()
    {
        $gruppi = Gruppo::select('id', 'nome')
            ->orderBy('nome')
            ->get()
            ->map(function($g) {
                return [
                    'id' => $g->id,
                    'nome' => $g->nome,
                    'full_path' => $g->getFullPathAttribute(),
                ];
            });
        return response()->json($gruppi);
    }

    public function individuiEmail(Gruppo $gruppo)
    {
        $individui = $gruppo->individui()->with('contatti')->get();
        
        $result = $individui->map(function($ind) {
            $emails = $ind->contatti->where('tipo', 'email')->pluck('valore')->toArray();
            return [
                'id' => $ind->id,
                'codice_id' => $ind->codice_id,
                'cognome' => $ind->cognome,
                'nome' => $ind->nome,
                'emails' => $emails,
            ];
        });
        
        return response()->json($result);
    }
}