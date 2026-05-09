@extends('layouts.adminlte')
@section('title', 'Dettaglio Gruppo')
@section('page_title', 'Dettaglio Gruppo')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('gruppi.index') }}">Gruppi</a></li>
<li class="breadcrumb-item active">{{ $gruppo->nome }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-folder text-warning mr-2"></i>
                    {{ $gruppo->nome }}
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td style="width: 130px;"><strong>Path:</strong></td>
                        <td class="text-muted small">{{ $gruppo->full_path }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gruppo Padre:</strong></td>
                        <td>
                            @if($gruppo->parent)
                                <a href="{{ route('gruppi.show', $gruppo->parent->id) }}">{{ $gruppo->parent->nome }}</a>
                            @else
                                <span class="badge badge-success">Gruppo radice</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Diocesi:</strong></td>
                        <td>{{ $gruppo->diocesi?->nome ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Responsabile:</strong></td>
                        <td>
                            @php
                                $responsabile = $gruppo->individui->firstWhere('id', $gruppo->responsabile_id);
                            @endphp
                            @if($responsabile)
                                <a href="{{ route('individui.show', $responsabile->id) }}">
                                    <i class="fas fa-user text-info mr-1"></i>
                                    {{ $responsabile->nome_completo }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Luogo Incontro</h3></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td style="width: 100px;"><strong>Indirizzo:</strong></td>
                        <td>{{ $gruppo->indirizzo_incontro ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>CAP:</strong></td>
                        <td>{{ $gruppo->cap_incontro ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Città:</strong></td>
                        <td>{{ $gruppo->città_incontro ?: '-' }} {{ $gruppo->sigla_provincia_incontro ? "({$gruppo->sigla_provincia_incontro})" : '' }}</td>
                    </tr>
                </table>
                @if($gruppo->mappa_posizione)
                    <a href="{{ $gruppo->mappa_posizione }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Visualizza su mappa
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Statistiche</h3></div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-right">
                            <h2 class="mb-0 text-primary">{{ $gruppo->individui->count() }}</h2>
                            <small class="text-muted">Membri</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h2 class="mb-0 text-warning">{{ $gruppo->children->count() }}</h2>
                        <small class="text-muted">Sottogruppi</small>
                    </div>
</div>
                </div>
            </div>
        </div>
    </div>

    @php
    $incontroEvento = $gruppo->eventi->where('is_incontro_gruppo', true)->first();
    @endphp
    @if($incontroEvento)
    <div class="row">
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Giorno di Incontro
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 100px;"><strong>Evento:</strong></td>
                            <td>{{ $incontroEvento->nome_evento }}</td>
                        </tr>
                        <tr>
                            <td><strong>Quando:</strong></td>
                            <td>
                                @if($incontroEvento->tipo_recorrenza === 'settimanale')
                                    {{ $incontroEvento->giorno_settimana_label }}
                                    <span class="badge badge-info ml-1">Settimanale</span>
                                @elseif($incontroEvento->tipo_recorrenza === 'mensile')
                                    {{ $incontroEvento->occorrenza_mensile_label }}
                                    @if($incontroEvento->mesi_recorrenza)
                                        <small class="text-muted">({{ $incontroEvento->mesi_recorrenza_label }})</small>
                                    @endif
                                    <span class="badge badge-info ml-1">Mensile</span>
                                @elseif($incontroEvento->tipo_recorrenza === 'annuale')
                                    {{ $incontroEvento->mese_annuale_label }}
                                    <span class="badge badge-info ml-1">Annuale</span>
                                @elseif($incontroEvento->tipo_recorrenza === 'altro')
                                    {{ $incontroEvento->giorno_settimana_label }}
                                    <span class="badge badge-info ml-1">Altro</span>
                                @else
                                    {{ $incontroEvento->data_specifica?->format('d/m/Y') ?: '-' }}
                                @endif
                            </td>
                        </tr>
                        @if($incontroEvento->ora_inizio)
                        <tr>
                            <td><strong>Ora:</strong></td>
                            <td>ore {{ $incontroEvento->ora_inizio->format('H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                    @if($incontroEvento->responsabili->count() > 0)
                    <hr>
                    <small class="text-muted"><strong>Responsabili:</strong></small>
                    @foreach($incontroEvento->responsabili as $resp)
                        <div class="mt-1">
                            <i class="fas fa-user text-info mr-1"></i>
                            <a href="{{ url('/individui/' . $resp->id) }}">{{ $resp->cognome }} {{ $resp->nome }}</a>
                            @if($resp->telefono_primario)
                                <br><small class="text-success ml-3">{{ $resp->telefono_primario }}</small>
                            @endif
                        </div>
                    @endforeach
                    @endif
                    <div class="mt-2">
                        <a href="{{ url('/eventi/' . $incontroEvento->id) }}" class="btn btn-xs btn-outline-primary">
                            <i class="fas fa-eye mr-1"></i> Vedi evento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($gruppo->descrizione)
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-align-left mr-2"></i>Descrizione</h3></div>
            <div class="card-body">
                <p class="mb-0">{!! nl2br(e($gruppo->descrizione)) !!}</p>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>Membri ({{ $gruppo->individui->count() }})
        </h3>
    </div>
    <div class="card-body p-0">
        @if($gruppo->individui->count() > 0)
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 100px;">Codice</th>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th style="width: 150px;">Ruolo</th>
                        <th style="width: 110px;">Data Adesione</th>
                        <th style="width: 80px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gruppo->individui as $individuo)
                    <tr>
                        <td><span class="badge badge-secondary">{{ $individuo->codice_id }}</span></td>
                        <td>
                            <a href="{{ route('individui.show', $individuo->id) }}">
                                <i class="fas fa-user text-info mr-1"></i>
                                {{ $individuo->cognome }} {{ $individuo->nome }}
                            </a>
                        </td>
                        <td>{{ $individuo->email_primaria ?? '-' }}</td>
                        <td>{{ $individuo->telefono_primario ?? '-' }}</td>
                        <td>
                            @if($individuo->pivot->ruolo_nel_gruppo)
                                <span class="badge badge-secondary">{{ $individuo->pivot->ruolo_nel_gruppo }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($individuo->pivot->data_adesione)
                                {{ \Carbon\Carbon::parse($individuo->pivot->data_adesione)->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('individui.show', $individuo->id) }}" class="btn btn-xs btn-info" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p class="mb-0">Nessun membro</p>
            </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file mr-2"></i>Documenti ({{ $gruppo->documenti->count() }})
        </h3>
        <a href="{{ url('/gruppi/' . $gruppo->id . '/edit') }}" class="btn btn-xs btn-primary float-right">
            <i class="fas fa-upload mr-1"></i> Carica
        </a>
    </div>
    <div class="card-body p-0">
        @if($gruppo->documenti->count() > 0)
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nome</th>
                        <th style="width: 120px;">Tipologia</th>
                        <th style="width: 80px;">Dimensione</th>
                        <th style="width: 130px;">Data Upload</th>
                        <th style="width: 100px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gruppo->documenti as $documento)
                    <tr>
                        <td>
                            <i class="fas fa-file text-secondary mr-1"></i>
                            {{ $documento->nome_file }}
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $documento->tipologia)) }}</td>
                        <td>{{ number_format($documento->dimensione / 1024, 1) }} KB</td>
                        <td>{{ $documento->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($documento->file_path)
                                <a href="{{ url('/documenti/' . $documento->id . '/download') }}" class="btn btn-xs btn-primary" title="Scarica">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-file fa-2x mb-2"></i>
                <p class="mb-0">Nessun documento</p>
            </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sitemap mr-2"></i>Sottogruppi ({{ $gruppo->children->count() }})
        </h3>
        <a href="{{ route('gruppi.create', ['parent_id' => $gruppo->id]) }}" class="btn btn-xs btn-success float-right">
            <i class="fas fa-plus mr-1"></i> Aggiungi Sottogruppo
        </a>
    </div>
    <div class="card-body p-0">
        @if($gruppo->children->count() > 0)
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nome</th>
                        <th>Diocesi</th>
                        <th>Responsabile</th>
                        <th style="text-align: center;">Membri</th>
                        <th style="width: 100px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gruppo->children->sortBy('nome') as $child)
                    <tr>
                        <td>
                            <a href="{{ route('gruppi.show', $child->id) }}">
                                <i class="fas fa-folder-open text-warning mr-1"></i>
                                {{ $child->nome }}
                            </a>
                        </td>
                        <td>{{ $child->diocesi?->nome ?? '-' }}</td>
                        <td>{{ $child->responsabile?->nome_completo ?? '-' }}</td>
                        <td class="text-center">
                            @if($child->individui()->count() > 0)
                                <span class="badge badge-info">{{ $child->individui()->count() }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('gruppi.show', $child->id) }}" class="btn btn-xs btn-info" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('gruppi.edit', $child->id) }}" class="btn btn-xs btn-warning" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('gruppi.destroy', $child->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare {{ $child->nome }}?')" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-folder-open fa-2x mb-2"></i>
                <p class="mb-0">Nessun sottogruppo</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ url('/gruppi/' . $gruppo->id . '/edit') }}" class="btn btn-warning">
        <i class="fas fa-edit mr-1"></i> Modifica
    </a>
    <form action="{{ url('/gruppi/' . $gruppo->id) }}" method="POST" class="d-inline">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Confermi l\'eliminazione di {{ $gruppo->nome }}? Verranno eliminati anche tutti i sottogruppi.')">
            <i class="fas fa-trash mr-1"></i> Elimina
        </button>
    </form>
    <a href="{{ route('gruppi.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Torna all'elenco
    </a>
</div>
@endsection
