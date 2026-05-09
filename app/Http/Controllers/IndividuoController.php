<?php

namespace App\Http\Controllers;

use App\Models\Individuo;
use App\Models\Contatto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IndividuoController extends Controller
{
    public function index(Request $request)
    {
        $individui = Individuo::with('contatti')->orderBy('cognome')->orderBy('nome')->paginate(20);

        $vista = null;

        if ($request->has('vista_id')) {
            $vista = \App\Models\VistaReport::where('id', $request->vista_id)
                ->where('user_id', auth()->id())
                ->first();
        }

        if (!$vista && auth()->check()) {
            $vista = \App\Models\VistaReport::where('user_id', auth()->id())
                ->where('tipo', 'individui')
                ->where('is_default', true)
                ->first();
        }

        return view('individui.index', compact('individui', 'vista'));
    }

    public function create()
    {
        return view('individui.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cognome' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'data_nascita' => 'nullable|date',
            'indirizzo' => 'nullable|string|max:500',
            'cap' => 'nullable|string|max:10',
            'città' => 'nullable|string|max:255',
            'sigla_provincia' => 'nullable|string|max:2',
            'genere' => 'nullable|in:M,F',
            'tipo_documento' => 'nullable|in:carta_identita,patente',
            'numero_documento' => 'nullable|string|max:50',
            'scadenza_documento' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $individuo = Individuo::create($data);

        if ($request->has('contatti')) {
            foreach ($request->contatti as $contatto) {
                if (!empty($contatto['tipo']) && !empty($contatto['valore'])) {
                    $individuo->contatti()->create([
                        'tipo' => $contatto['tipo'],
                        'valore' => $contatto['valore'],
                        'etichetta' => $contatto['etichetta'] ?? null,
                        'is_primary' => $contatto['is_primary'] ?? false,
                    ]);
                }
            }
        }

        return redirect()->route('individui.index')->with('success', 'Individuo creato con successo.');
    }

    public function show($individuo)
    {
        $individuo = Individuo::with(['contatti', 'gruppi'])->findOrFail($individuo);
        return view('individui.show', compact('individuo'));
    }

    public function report($individuo)
    {
        $individuo = Individuo::with(['contatti', 'gruppi', 'documenti', 'eventiResponsabili'])->findOrFail($individuo);
        return view('individui.report', compact('individuo'));
    }

    public function edit($individuo)
    {
        $individuo = Individuo::with('contatti')->findOrFail($individuo);
        return view('individui.edit', compact('individuo'));
    }

    public function update(Request $request, $individuo)
    {
        $individuo = Individuo::findOrFail($individuo);
        $data = $request->validate([
            'cognome' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'data_nascita' => 'nullable|date',
            'indirizzo' => 'nullable|string|max:500',
            'cap' => 'nullable|string|max:10',
            'città' => 'nullable|string|max:255',
            'sigla_provincia' => 'nullable|string|max:2',
            'genere' => 'nullable|in:M,F',
            'tipo_documento' => 'nullable|in:carta_identita,patente',
            'numero_documento' => 'nullable|string|max:50',
            'scadenza_documento' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $individuo->update($data);

        if ($request->has('contatti')) {
            $individuo->contatti()->delete();
            foreach ($request->contatti as $contatto) {
                if (!empty($contatto['tipo']) && !empty($contatto['valore'])) {
                    $individuo->contatti()->create([
                        'tipo' => $contatto['tipo'],
                        'valore' => $contatto['valore'],
                        'etichetta' => $contatto['etichetta'] ?? null,
                        'is_primary' => $contatto['is_primary'] ?? false,
                    ]);
                }
            }
        }

        return redirect()->route('individui.show', $individuo)->with('success', 'Individuo aggiornato.');
    }

    public function destroy($individuo)
    {
        $individuo = Individuo::findOrFail($individuo);
        $individuo->delete();
        return redirect()->route('individui.index')->with('success', 'Individuo eliminato.');
    }

    public function elementiCollegati($individuo)
    {
        $individuo = Individuo::findOrFail($individuo);

        return response()->json([
            'contatti' => $individuo->contatti()->count(),
            'gruppi' => $individuo->gruppi()->count(),
            'documenti' => $individuo->documenti()->count(),
            'eventi' => $individuo->eventiResponsabili()->count(),
        ]);
    }

public function emailList(Request $request)
    {
        $ids = $request->input('ids', '');
        $idArray = array_filter(array_map('intval', explode(',', $ids)));

        if (empty($idArray)) {
            return response()->json([]);
        }

        $individui = Individuo::with('contatti')->whereIn('id', $idArray)->get();

        $result = [];
        foreach ($individui as $ind) {
            $emails = $ind->contatti->where('tipo', 'email')->pluck('valore')->toArray();
            $result[] = [
                'id' => $ind->id,
                'codice_id' => $ind->codice_id,
                'cogname' => $ind->cogname,
                'nome' => $ind->nome,
                'email' => count($emails) === 1 ? $emails[0] : null,
                'emails' => $emails,
                'email_count' => count($emails)
            ];
        }

        return response()->json($result);
    }

    public function allIndividui()
    {
        $individui = Individuo::select('id', 'codice_id', 'cognome', 'nome')
            ->orderBy('cognome')
            ->orderBy('nome')
            ->get();
        return response()->json($individui);
    }

    public function import()
    {
        return view('individui.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->path(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNumber++;

            if (count($row) < count($header)) {
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);

            if ($data === false) {
                $skipped++;
                continue;
            }

            $cognome = trim($data['cognome'] ?? '');
            $nome = trim($data['nome'] ?? '');

            if (empty($cognome) || empty($nome)) {
                $skipped++;
                continue;
            }

            try {
                $individuo = Individuo::create([
                    'cognome' => $cognome,
                    'nome' => $nome,
                    'data_nascita' => !empty(trim($data['data_nascita'] ?? '')) ? trim($data['data_nascita']) : null,
                    'indirizzo' => !empty(trim($data['indirizzo'] ?? '')) ? trim($data['indirizzo']) : null,
                    'cap' => !empty(trim($data['cap'] ?? '')) ? trim($data['cap']) : null,
                    'città' => !empty(trim($data['città'] ?? '')) ? trim($data['città']) : null,
                    'sigla_provincia' => !empty(trim($data['sigla_provincia'] ?? '')) ? trim($data['sigla_provincia']) : null,
                    'genere' => !empty(trim($data['genere'] ?? '')) ? strtoupper(trim($data['genere'])) : null,
                    'tipo_documento' => !empty(trim($data['tipo_documento'] ?? '')) ? trim($data['tipo_documento']) : null,
                    'numero_documento' => !empty(trim($data['numero_documento'] ?? '')) ? trim($data['numero_documento']) : null,
                    'scadenza_documento' => !empty(trim($data['scadenza_documento'] ?? '')) ? trim($data['scadenza_documento']) : null,
                    'note' => !empty(trim($data['note'] ?? '')) ? trim($data['note']) : null,
                ]);

                for ($i = 1; $i <= 2; $i++) {
                    $tipoKey = 'contatto_' . $i . '_tipo';
                    $valoreKey = 'contatto_' . $i . '_valore';
                    $etichettaKey = 'contatto_' . $i . '_etichetta';

                    $tipo = trim($data[$tipoKey] ?? '');
                    $valore = trim($data[$valoreKey] ?? '');

                    if (!empty($tipo) && !empty($valore)) {
                        $individuo->contatti()->create([
                            'tipo' => $tipo,
                            'valore' => $valore,
                            'etichetta' => !empty(trim($data[$etichettaKey] ?? '')) ? trim($data[$etichettaKey]) : null,
                            'is_primary' => $i === 1,
                        ]);
                    }
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = 'Errore riga ' . $rowNumber . ': ' . $e->getMessage();
            }
        }

        fclose($handle);

        $message = 'Importati ' . $imported . ' individui.';
        if ($skipped > 0) {
            $message .= ' Saltate ' . $skipped . ' righe (vuote o incomplete).';
        }
        if (count($errors) > 0) {
            return back()->with('error', $message . ' Errori: ' . implode('; ', $errors));
        }

        return redirect()->route('individui.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = ['cognome', 'nome', 'data_nascita', 'indirizzo', 'cap', 'città', 'sigla_provincia', 'genere', 'tipo_documento', 'numero_documento', 'scadenza_documento', 'note', 'contatto_1_tipo', 'contatto_1_valore', 'contatto_1_etichetta', 'contatto_2_tipo', 'contatto_2_valore', 'contatto_2_etichetta'];

        $example = [
            'Rossi',
            'Mario',
            '1990-05-15',
            'Via Roma 10',
            '00100',
            'Roma',
            'RM',
            'M',
            'carta_identita',
            'AB123456',
            '2030-05-15',
            'Socio fondatore',
            'email',
            'mario.rossi@email.com',
            'personale',
            'cellulare',
            '3331234567',
            'lavoro',
        ];

        $csv = implode(',', $headers) . "\n" . implode(',', $example);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_individui.csv"',
        ]);
    }
}