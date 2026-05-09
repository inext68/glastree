<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Gruppo;
use App\Models\Individuo;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $query = Evento::with(['gruppi', 'responsabili']);

        if ($request->filled('gruppo_id')) {
            $query->whereHas('gruppi', fn($q) => $q->where('gruppi.id', $request->gruppo_id));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_recorrenza', $request->tipo);
        }

        $eventi = $query->orderByDesc('created_at')->paginate(20);
        $gruppi = Gruppo::orderBy('nome')->get();

        return view('eventi.index', compact('eventi', 'gruppi'));
    }

    public function create(Request $request)
    {
        $gruppi = Gruppo::orderBy('nome')->get();
        $individui = Individuo::orderBy('cognome')->orderBy('nome')->get();
        $selectedGruppo = $request->query('gruppo_id') ? Gruppo::find($request->query('gruppo_id')) : null;

        return view('eventi.create', compact('gruppi', 'individui', 'selectedGruppo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_evento' => 'required|string|max:255',
            'descrizione_evento' => 'nullable|string|max:255',
            'tipo_evento' => 'nullable|string|max:100',
            'tipo_recorrenza' => 'nullable|in:singolo,settimanale,mensile,annuale,altro',
            'giorno_settimana' => 'nullable|integer|min:0|max:6',
            'giorno_mese' => 'nullable|integer|min:1|max:31',
            'occorrenza_mese' => 'nullable|string|max:10',
            'mesi_recorrenza' => 'nullable|string',
            'mese_annuale' => 'nullable|integer|min:1|max:12',
            'ora_inizio' => 'nullable|date_format:H:i',
            'data_specifica' => 'nullable|date',
            'durata_minuti' => 'nullable|integer|min:1',
            'descrizione' => 'nullable|string',
            'note' => 'nullable|string',
            'is_incontro_gruppo' => 'nullable|boolean',
            'gruppi' => 'nullable|array',
            'gruppi.*' => 'exists:gruppi,id',
            'responsabili' => 'nullable|array',
            'responsabili.*' => 'exists:individui,id',
        ]);

        $evento = Evento::create([
            'nome_evento' => $data['nome_evento'],
            'descrizione_evento' => $data['descrizione_evento'] ?? null,
            'tipo_evento' => $data['tipo_evento'] ?? null,
            'tipo_recorrenza' => $data['tipo_recorrenza'] ?? 'singolo',
            'giorno_settimana' => $data['giorno_settimana'] ?? null,
            'giorno_mese' => $data['giorno_mese'] ?? null,
            'occorrenza_mese' => $data['occorrenza_mese'] ?? null,
            'mesi_recorrenza' => is_array($request->mesi_recorrenza) ? implode(',', $request->mesi_recorrenza) : ($data['mesi_recorrenza'] ?? null),
            'mese_annuale' => $data['mese_annuale'] ?? null,
            'ora_inizio' => $data['ora_inizio'] ?? null,
            'data_specifica' => $data['data_specifica'] ?? null,
            'durata_minuti' => $data['durata_minuti'] ?? null,
            'descrizione' => $data['descrizione'] ?? null,
            'note' => $data['note'] ?? null,
            'is_incontro_gruppo' => $request->boolean('is_incontro_gruppo'),
        ]);

        if (!empty($data['gruppi'])) {
            $evento->gruppi()->attach($data['gruppi']);
        }

        if (!empty($data['responsabili'])) {
            $evento->responsabili()->attach($data['responsabili']);
        }

        return redirect('/eventi/' . $evento->id)->with('success', 'Evento creato con successo.');
    }

    public function show($evento)
    {
        $evento = Evento::with(['gruppi', 'responsabili.contatti', 'documenti'])->findOrFail($evento);
        return view('eventi.show', compact('evento'));
    }

    public function edit($evento)
    {
        $evento = Evento::with(['gruppi', 'responsabili', 'documenti'])->findOrFail($evento);
        $gruppi = Gruppo::orderBy('nome')->get();
        $individui = Individuo::orderBy('cognome')->orderBy('nome')->get();

        return view('eventi.edit', compact('evento', 'gruppi', 'individui'));
    }

    public function update(Request $request, $evento)
    {
        $evento = Evento::findOrFail($evento);

        $data = $request->validate([
            'nome_evento' => 'required|string|max:255',
            'descrizione_evento' => 'nullable|string|max:255',
            'tipo_evento' => 'nullable|string|max:100',
            'tipo_recorrenza' => 'nullable|in:singolo,settimanale,mensile,annuale,altro',
            'giorno_settimana' => 'nullable|integer|min:0|max:6',
            'giorno_mese' => 'nullable|integer|min:1|max:31',
            'occorrenza_mese' => 'nullable|string|max:10',
            'mesi_recorrenza' => 'nullable|string',
            'mese_annuale' => 'nullable|integer|min:1|max:12',
            'ora_inizio' => 'nullable|date_format:H:i',
            'data_specifica' => 'nullable|date',
            'durata_minuti' => 'nullable|integer|min:1',
            'descrizione' => 'nullable|string',
            'note' => 'nullable|string',
            'is_incontro_gruppo' => 'nullable|boolean',
            'gruppi' => 'nullable|array',
            'gruppi.*' => 'exists:gruppi,id',
            'responsabili' => 'nullable|array',
            'responsabili.*' => 'exists:individui,id',
        ]);

        $evento->update([
            'nome_evento' => $data['nome_evento'],
            'descrizione_evento' => $data['descrizione_evento'] ?? null,
            'tipo_evento' => $data['tipo_evento'] ?? null,
            'tipo_recorrenza' => $data['tipo_recorrenza'] ?? 'singolo',
            'giorno_settimana' => $data['giorno_settimana'] ?? null,
            'giorno_mese' => $data['giorno_mese'] ?? null,
            'occorrenza_mese' => $data['occorrenza_mese'] ?? null,
            'mesi_recorrenza' => is_array($request->mesi_recorrenza) ? implode(',', $request->mesi_recorrenza) : ($data['mesi_recorrenza'] ?? null),
            'mese_annuale' => $data['mese_annuale'] ?? null,
            'ora_inizio' => $data['ora_inizio'] ?? null,
            'data_specifica' => $data['data_specifica'] ?? null,
            'durata_minuti' => $data['durata_minuti'] ?? null,
            'descrizione' => $data['descrizione'] ?? null,
            'note' => $data['note'] ?? null,
            'is_incontro_gruppo' => $request->boolean('is_incontro_gruppo'),
        ]);

        $evento->gruppi()->sync($data['gruppi'] ?? []);
        $evento->responsabili()->sync($data['responsabili'] ?? []);

        return redirect('/eventi/' . $evento->id)->with('success', 'Evento aggiornato.');
    }

    public function destroy($evento)
    {
        $evento = Evento::findOrFail($evento);
        $evento->delete();
        return redirect('/eventi')->with('success', 'Evento eliminato.');
    }
}