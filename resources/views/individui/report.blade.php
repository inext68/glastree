<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheda Individuo - {{ $individuo->cognome }} {{ $individuo->nome }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background: #2c3e50;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
            width: 150px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background: #6c757d;
            color: #fff;
            border-radius: 3px;
            font-size: 11px;
        }
        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; }
        .badge-info { background: #17a2b8; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .info-item {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .info-item label {
            display: block;
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .empty {
            color: #999;
            font-style: italic;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        @media print {
            body { padding: 0; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $individuo->cognome }} {{ $individuo->nome }}</h1>
        <div class="subtitle">Scheda Individuo - Codice: {{ $individuo->codice_id }}</div>
    </div>

    <div class="section">
        <div class="section-title">Dati Anagrafici</div>
        <table>
            <tr>
                <th>Codice</th>
                <td><span class="badge">{{ $individuo->codice_id }}</span></td>
                <th>Cognome</th>
                <td>{{ $individuo->cognome }}</td>
            </tr>
            <tr>
                <th>Nome</th>
                <td>{{ $individuo->nome }}</td>
                <th>Genere</th>
                <td>{{ $individuo->genere === 'M' ? 'Maschile' : ($individuo->genere === 'F' ? 'Femminile' : '-') }}</td>
            </tr>
            <tr>
                <th>Data di nascita</th>
                <td>{{ $individuo->data_nascita ? $individuo->data_nascita->format('d/m/Y') : '-' }}</td>
                <th>Luogo di nascita</th>
                <td>{{ $individuo->città ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Residenza</div>
        <div class="info-grid">
            <div class="info-item">
                <label>Indirizzo</label>
                {{ $individuo->indirizzo ?: '-' }}
            </div>
            <div class="info-item">
                <label>CAP</label>
                {{ $individuo->cap ?: '-' }}
            </div>
            <div class="info-item">
                <label>Città</label>
                {{ $individuo->città ?: '-' }}
            </div>
            <div class="info-item">
                <label>Provincia</label>
                {{ $individuo->sigla_provincia ?: '-' }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Documento di Identità</div>
        @if($individuo->tipo_documento || $individuo->numero_documento)
        <table>
            <tr>
                <th>Tipo</th>
                <td>
                    @if($individuo->tipo_documento === 'carta_identita')
                        <span class="badge badge-info">Carta d'Identità</span>
                    @elseif($individuo->tipo_documento === 'patente')
                        <span class="badge badge-warning">Patente</span>
                    @else
                        {{ $individuo->tipo_documento }}
                    @endif
                </td>
                <th>Numero</th>
                <td>{{ $individuo->numero_documento }}</td>
            </tr>
            <tr>
                <th>Scadenza</th>
                <td>
                    @if($individuo->scadenza_documento)
                        @if($individuo->scadenza_documento->isPast())
                            <span class="badge badge-danger">SCADUTO</span>
                        @else
                            {{ $individuo->scadenza_documento->format('d/m/Y') }}
                        @endif
                    @else
                        -
                    @endif
                </td>
                <th></th>
                <td></td>
            </tr>
        </table>
        @else
        <p class="empty">Nessun documento registrato</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Contatti ({{ $individuo->contatti->count() }})</div>
        @if($individuo->contatti->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Valore</th>
                    <th>Etichetta</th>
                    <th>Primario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuo->contatti as $contatto)
                <tr>
                    <td>
                        @switch($contatto->tipo)
                            @case('email')
                                <span class="badge badge-info">Email</span>
                                @break
                            @case('telefono')
                                <span class="badge">Telefono</span>
                                @break
                            @case('cellulare')
                                <span class="badge badge-success">Cellulare</span>
                                @break
                            @default
                                {{ $contatto->tipo }}
                        @endswitch
                    </td>
                    <td>{{ $contatto->valore }}</td>
                    <td>{{ $contatto->etichetta ?: '-' }}</td>
                    <td>{{ $contatto->is_primary ? '✓' : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="empty">Nessun contatto registrato</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Gruppi di Appartenenza ({{ $individuo->gruppi->count() }})</div>
        @if($individuo->gruppi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Gruppo</th>
                    <th>Ruolo</th>
                    <th>Data Adesione</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuo->gruppi as $gruppo)
                <tr>
                    <td>{{ $gruppo->nome }}</td>
                    <td>{{ $gruppo->pivot->ruolo_nel_gruppo ?: '-' }}</td>
                    <td>{{ $gruppo->pivot->data_adesione ? \Carbon\Carbon::parse($gruppo->pivot->data_adesione)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="empty">Nessun gruppo associato</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Documenti ({{ $individuo->documenti->count() }})</div>
        @if($individuo->documenti->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipologia</th>
                    <th>Dimensione</th>
                    <th>Data Upload</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuo->documenti as $documento)
                <tr>
                    <td>{{ $documento->nome }}</td>
                    <td>{{ $documento->tipologia }}</td>
                    <td>{{ $documento->dimensione ? number_format($documento->dimensione / 1024, 1) . ' KB' : '-' }}</td>
                    <td>{{ $documento->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="empty">Nessun documento caricato</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Eventi come Responsabile ({{ $individuo->eventiResponsabili->count() }})</div>
        @if($individuo->eventiResponsabili->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Data</th>
                    <th>Tipo Ricorrenza</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuo->eventiResponsabili as $evento)
                <tr>
                    <td>{{ $evento->nome }}</td>
                    <td>{{ $evento->data_specifica ? $evento->data_specifica->format('d/m/Y') : '-' }}</td>
                    <td>{{ $evento->tipo_recorrenza ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="empty">Nessun evento assegnato come responsabile</p>
        @endif
    </div>

    @if($individuo->note)
    <div class="section">
        <div class="section-title">Note</div>
        <p>{{ $individuo->note }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Report generato il {{ now()->format('d/m/Y H:i') }} - Glastree</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>