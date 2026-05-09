@extends('layouts.adminlte')
@section('title', $evento->nome_evento)
@section('page_title', 'Dettaglio Evento')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ url('/eventi') }}">Eventi</a></li>
<li class="breadcrumb-item active">{{ $evento->nome_evento }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ $evento->nome_evento }}
                </h3>
            </div>
            <div class="card-body">
                @if($evento->descrizione_evento)
                <p class="lead">{{ $evento->descrizione_evento }}</p>
                @endif

                @if($evento->descrizione)
                <div class="mt-3">
                    <h5>Descrizione Completa</h5>
                    <p>{!! nl2br(e($evento->descrizione)) !!}</p>
                </div>
                @endif

                @if($evento->is_incontro_gruppo)
                <div class="alert alert-success mt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Evento di incontro gruppo</strong>
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-clock mr-2"></i>Data e Orario</h3></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td style="width: 150px;"><strong>Tipo:</strong></td>
                        <td>
                            @if($evento->tipo_recorrenza && $evento->tipo_recorrenza !== 'singolo')
                                <span class="badge badge-info">{{ $evento->periodicita_label }}</span>
                            @else
                                <span class="badge badge-secondary">Singolo</span>
                            @endif
                        </td>
                    </tr>
                    @if($evento->tipo_recorrenza === 'settimanale')
                    <tr>
                        <td><strong>Giorno:</strong></td>
                        <td>{{ $evento->giorno_settimana_label }}</td>
                    </tr>
                    @elseif($evento->tipo_recorrenza === 'mensile')
                    <tr>
                        <td><strong>Occorrenza:</strong></td>
                        <td>{{ $evento->occorrenza_mensile_label }}</td>
                    </tr>
                    <tr>
                        <td><strong>Mesi:</strong></td>
                        <td>{{ $evento->mesi_recorrenza_label }}</td>
                    </tr>
                    @elseif($evento->tipo_recorrenza === 'annuale')
                    <tr>
                        <td><strong>Mese:</strong></td>
                        <td>{{ $evento->mese_annuale_label }}</td>
                    </tr>
                    @elseif($evento->tipo_recorrenza === 'altro')
                    <tr>
                        <td><strong>Giorno:</strong></td>
                        <td>{{ $evento->giorno_settimana_label }}</td>
                    </tr>
                    @else
                    <tr>
                        <td><strong>Data:</strong></td>
                        <td>{{ $evento->data_specifica?->format('d/m/Y') ?: '-' }}</td>
                    </tr>
                    @endif
                    @if($evento->ora_inizio)
                    <tr>
                        <td><strong>Ora Inizio:</strong></td>
                        <td>{{ $evento->ora_inizio->format('H:i') }}</td>
                    </tr>
                    @endif
                    @if($evento->durata_minuti)
                    <tr>
                        <td><strong>Durata:</strong></td>
                        <td>{{ $evento->durata_minuti }} minuti</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-folder mr-2"></i>Gruppi</h3></div>
            <div class="card-body p-0">
                @if($evento->gruppi->count() > 0)
                    <table class="table table-sm mb-0">
                        <tbody>
                            @foreach($evento->gruppi as $gruppo)
                            <tr>
                                <td>
                                    <a href="{{ url('/gruppi/' . $gruppo->id) }}">
                                        <i class="fas fa-folder text-warning mr-1"></i>
                                        {{ $gruppo->nome }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted py-3 mb-0">
                        <span>Nessun gruppo associato</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Responsabili</h3></div>
            <div class="card-body p-0">
                @if($evento->responsabili->count() > 0)
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Contatto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evento->responsabili as $resp)
                            <tr>
                                <td>
                                    <a href="{{ url('/individui/' . $resp->id) }}">
                                        <i class="fas fa-user text-info mr-1"></i>
                                        {{ $resp->cognome }} {{ $resp->nome }}
                                    </a>
                                </td>
                                <td>
                                    @if($resp->telefono_primario)
                                        <small class="text-success">{{ $resp->telefono_primario }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted py-3 mb-0">
                        <span>Nessun responsabile</span>
                    </div>
                @endif
            </div>
        </div>

        @if($evento->note)
        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Note</h3></div>
            <div class="card-body">
                <p class="mb-0">{!! nl2br(e($evento->note)) !!}</p>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file mr-2"></i>Documenti</h3>
                <button type="button" class="btn btn-xs btn-success float-right" onclick="showDocumentoForm()">
                    <i class="fas fa-plus mr-1"></i> Carica
                </button>
            </div>
            <div class="card-body p-0">
                <div id="documento-add-form" style="display:none;" class="p-3 bg-light border-bottom">
                    <form action="{{ url('/eventi/' . $evento->id . '/documenti') }}" method="POST" enctype="multipart/form-data" class="mb-0">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="nome_file" class="form-control form-control-sm" placeholder="Nome documento" required>
                            </div>
                            <div class="col-md-3">
                                <select name="tipologia" class="form-control form-control-sm" required>
                                    <option value="">Tipologia...</option>
                                    <option value="documento">Documento</option>
                                    <option value="programma">Programma</option>
                                    <option value="locandina">Locandina</option>
                                    <option value="altro">Altro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="file" name="file" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-upload mr-1"></i>Carica</button>
                                <button type="button" class="btn btn-xs btn-secondary" onclick="hideDocumentoForm()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

                @if($evento->documenti && $evento->documenti->count() > 0)
                <table class="table table-bordered table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nome</th>
                            <th style="width: 120px;">Tipologia</th>
                            <th style="width: 80px;">Dimensione</th>
                            <th style="width: 130px;">Data Upload</th>
                            <th style="width: 80px;">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evento->documenti as $documento)
                        <tr>
                            <td>
                                <i class="fas fa-file text-secondary mr-1"></i>
                                {{ $documento->nome_file }}
                            </td>
                            <td>{{ ucfirst($documento->tipologia) }}</td>
                            <td>{{ number_format($documento->dimensione / 1024, 1) }} KB</td>
                            <td>{{ $documento->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($documento->file_path)
                                    <button type="button" class="btn btn-xs btn-primary" onclick="previewDocumento({{ $documento->id }})" title="Anteprima">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
                                <form action="{{ url('/eventi/' . $evento->id . '/documenti/' . $documento->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare questo documento?')" title="Elimina">
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
                    <i class="fas fa-file fa-2x mb-2"></i>
                    <p class="mb-0">Nessun documento</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ url('/eventi/' . $evento->id . '/edit') }}" class="btn btn-warning">
        <i class="fas fa-edit mr-1"></i> Modifica
    </a>
    <form action="{{ url('/eventi/' . $evento->id) }}" method="POST" class="d-inline">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Confermi l\'eliminazione di {{ $evento->nome_evento }}?')">
            <i class="fas fa-trash mr-1"></i> Elimina
        </button>
    </form>
    <a href="{{ url('/eventi') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Torna all'elenco
    </a>
</div>
@endsection

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 90vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file mr-2"></i><span id="previewModalTitle">Anteprima</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0" style="min-height: 400px;">
                <iframe id="previewFrame" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <a id="previewDownloadBtn" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download mr-1"></i> Scarica
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function showDocumentoForm() {
    document.getElementById('documento-add-form').style.display = 'block';
}

function hideDocumentoForm() {
    document.getElementById('documento-add-form').style.display = 'none';
}

function previewDocumento(id) {
    document.getElementById('previewFrame').src = '{{ url('/documenti/') }}/' + id + '/preview';
    document.getElementById('previewDownloadBtn').href = '{{ url('/documenti/') }}/' + id + '/download';
    $('#previewModal').modal('show');
}
</script>
@endsection