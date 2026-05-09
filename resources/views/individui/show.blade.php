@extends('layouts.adminlte')
@section('title', 'Dettaglio Individuo')
@section('page_title', 'Dettaglio Individuo')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('individui.index') }}">Individui</a></li>
<li class="breadcrumb-item active">{{ $individuo->nome_completo }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user mr-2"></i>{{ $individuo->nome_completo }}</h3>
            </div>
            <div class="card-body">
                <p><strong>Codice:</strong> <span class="badge badge-secondary">{{ $individuo->codice_id }}</span></p>
                <p><strong>Data di nascita:</strong> {{ $individuo->data_nascita?->format('d/m/Y') ?: '-' }}</p>
                <p><strong>Genere:</strong> {{ $individuo->genere === 'M' ? 'Maschio' : ($individuo->genere === 'F' ? 'Femmina' : '-') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Residenza</h3></div>
            <div class="card-body">
                <p><strong>Indirizzo:</strong> {{ $individuo->indirizzo ?: '-' }}</p>
                <p><strong>CAP:</strong> {{ $individuo->cap ?: '-' }}</p>
                <p><strong>Città:</strong> {{ $individuo->città ?: '-' }}</p>
                <p><strong>Provincia:</strong> {{ $individuo->sigla_provincia ?: '-' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-id-card mr-2"></i>Documento di identità</h3></div>
            <div class="card-body">
                <p><strong>Tipo:</strong> {{ $individuo->tipo_documento ? ucfirst(str_replace('_', ' ', $individuo->tipo_documento)) : '-' }}</p>
                <p><strong>Numero:</strong> {{ $individuo->numero_documento ?: '-' }}</p>
                <p><strong>Scadenza:</strong> {{ $individuo->scadenza_documento?->format('d/m/Y') ?: '-' }}</p>
                @if($individuo->hasDocumentoScaduto())
                    <p><span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Documento scaduto da {{ $individuo->giorni_scadenza_documento }} giorni</span></p>
                @elseif($individuo->hasDocumentoScadeEntroGiorni(30))
                    <p><span class="badge badge-warning"><i class="fas fa-exclamation-circle mr-1"></i> Scade tra {{ $individuo->giorni_scadenza_documento }} giorni</span></p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($individuo->note)
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Note</h3></div>
            <div class="card-body">
                <p class="mb-0">{!! nl2br(e($individuo->note)) !!}</p>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-address-book mr-2"></i>Contatti</h3>
    </div>
    <div class="card-body p-0">
        @if($individuo->contatti->count() > 0)
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 20%;">Tipo</th>
                    <th style="width: 30%;">Valore</th>
                    <th style="width: 20%;">Etichetta</th>
                    <th style="width: 10%;">Primario</th>
                    <th style="width: 80px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuo->contatti as $contatto)
                @php
$protocols = ['http://', 'https://', 'ftp://', 'ssh://', 'sftp://', 'telnet://'];
$isUrl = false;
if (in_array($contatto->tipo, ['web', 'telegram'])) {
    foreach ($protocols as $p) {
        if (str_starts_with(strtolower($contatto->valore), $p)) {
            $isUrl = true;
            break;
        }
    }
}
@endphp
                <tr id="contatto-row-{{ $contatto->id }}">
                    <td><span class="text-capitalize">{{ $contatto->tipo }}</span></td>
                    <td>
                        @if($contatto->tipo === 'email')
                            <a href="mailto:{{ $contatto->valore }}">{{ $contatto->valore }}</a>
                        @elseif(in_array($contatto->tipo, ['telefono', 'cellulare', 'whatsapp']))
                            <a href="tel:{{ $contatto->valore }}">{{ $contatto->valore }}</a>
                        @elseif($isUrl)
                            <a href="{{ $contatto->valore }}" target="_blank">{{ $contatto->valore }}</a>
                        @else
                            {{ $contatto->valore }}
                        @endif
                    </td>
                    <td>{{ $contatto->etichetta ?: '-' }}</td>
                    <td>
                        @if($contatto->is_primary)
                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Sì</span>
                        @else
                            <span class="text-muted">No</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-warning" onclick="editContattoInline({{ $contatto->id }})" title="Modifica">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ url('/contatti/' . $contatto->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <input type="hidden" name="_redirect" value="{{ url('/individui/' . $individuo->id) }}">
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare questo contatto?')" title="Elimina">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="contatto-edit-{{ $contatto->id }}" style="display:none;">
                    <td colspan="5">
                        <form action="{{ url('/contatti/' . $contatto->id) }}" method="POST" class="mb-0">
                            @csrf @method('PUT')
                            <input type="hidden" name="_redirect" value="{{ url('/individui/' . $individuo->id) }}">
                            <input type="hidden" name="individuo_id" value="{{ $individuo->id }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="tipo" class="form-control form-control-sm" required>
                                        <option value="telefono" {{ $contatto->tipo === 'telefono' ? 'selected' : '' }}>Telefono</option>
                                        <option value="cellulare" {{ $contatto->tipo === 'cellulare' ? 'selected' : '' }}>Cellulare</option>
                                        <option value="email" {{ $contatto->tipo === 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="fax" {{ $contatto->tipo === 'fax' ? 'selected' : '' }}>Fax</option>
                                        <option value="web" {{ $contatto->tipo === 'web' ? 'selected' : '' }}>Web</option>
                                        <option value="telegram" {{ $contatto->tipo === 'telegram' ? 'selected' : '' }}>Telegram</option>
                                        <option value="whatsapp" {{ $contatto->tipo === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="altro" {{ $contatto->tipo === 'altro' ? 'selected' : '' }}>Altro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="valore" class="form-control form-control-sm" value="{{ $contatto->valore }}" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="etichetta" class="form-control form-control-sm" value="{{ $contatto->etichetta }}" placeholder="Etichetta">
                                </div>
                                <div class="col-md-1 text-center">
                                    <input type="checkbox" name="is_primary" value="1" {{ $contatto->is_primary ? 'checked' : '' }}>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-check mr-1"></i>Salva</button>
                                    <button type="button" class="btn btn-xs btn-secondary" onclick="cancelEditContatto({{ $contatto->id }})"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-address-book fa-2x mb-2"></i>
            <p class="mb-0">Nessun contatto</p>
        </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder mr-2"></i>Gruppi</h3>
        <button type="button" class="btn btn-xs btn-success float-right" onclick="showGruppoForm()">
            <i class="fas fa-plus mr-1"></i> Aggiungi
        </button>
    </div>
    <div class="card-body p-0">
        
        @if($individuo->gruppi->count() > 0)
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Nome</th>
                    <th>Diocesi</th>
                    <th>Responsabile</th>
                    <th style="width: 130px;">Ruolo</th>
                    <th style="width: 110px;">Data Adesione</th>
                    <th style="width: 80px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuo->gruppi as $gruppo)
                <tr id="gruppo-row-{{ $gruppo->id }}">
                    <td>
                        <i class="fas fa-folder text-warning mr-1"></i>
                        <a href="{{ url('/gruppi/' . $gruppo->id) }}">{{ $gruppo->nome }}</a>
                    </td>
                    <td>{{ $gruppo->diocesi?->nome ?: '-' }}</td>
                    <td>{{ $gruppo->responsabile?->nome_completo ?? '-' }}</td>
                    <td>{{ $gruppo->pivot->ruolo_nel_gruppo ?: '-' }}</td>
                    <td>{{ $gruppo->pivot->data_adesione ? \Carbon\Carbon::parse($gruppo->pivot->data_adesione)->format('d/m/Y') : '-' }}</td>
                    <td>
                        <button type="button" class="btn btn-xs btn-warning" onclick="editGruppoInline({{ $gruppo->id }})" title="Modifica ruolo">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger" onclick="deleteGruppo({{ $gruppo->id }})" title="Rimuovi">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr id="gruppo-edit-{{ $gruppo->id }}" style="display:none;">
                    <td colspan="6">
                        <form action="{{ url('/individui/' . $individuo->id . '/gruppi/' . $gruppo->id) }}" method="POST" class="mb-0">
                            @csrf @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" value="{{ $gruppo->nome }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="ruolo_nel_gruppo" class="form-control form-control-sm" value="{{ $gruppo->pivot->ruolo_nel_gruppo }}" placeholder="Ruolo">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="data_adesione" class="form-control form-control-sm" value="{{ $gruppo->pivot->data_adesione }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-check mr-1"></i>Salva</button>
                                    <button type="button" class="btn btn-xs btn-secondary" onclick="cancelEditGruppo({{ $gruppo->id }})"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-users fa-2x mb-2"></i>
            <p class="mb-0">Nessun gruppo associato</p>
        </div>
        @endif
    </div>
</div>

<div class="card mt-3" id="gruppo-add-card" style="display:none; background-color: #fff3cd; border-color: #ffc107;">
    <div class="card-header bg-warning">
        <h3 class="card-title"><i class="fas fa-folder-plus mr-2"></i>Aggiungi a Gruppo</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <select id="new-gruppo-id" class="form-control form-control-sm">
                    <option value="">Seleziona gruppo...</option>
                    @foreach(\App\Models\Gruppo::orderBy('nome')->get() as $g)
                        @if(!$individuo->gruppi->contains($g->id))
                            <option value="{{ $g->id }}">{{ $g->full_path }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" id="new-gruppo-ruolo" class="form-control form-control-sm" placeholder="Ruolo">
            </div>
            <div class="col-md-3">
                <input type="date" id="new-gruppo-data" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-sm" onclick="submitGruppoForm()">
                    <i class="fas fa-check mr-1"></i> Associa
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="hideGruppoForm()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
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
            <form action="{{ url('/documenti') }}" method="POST" enctype="multipart/form-data" class="mb-0">
                @csrf
                <input type="hidden" name="visibilita" value="individuo">
                <input type="hidden" name="visibilita_target_id" value="{{ $individuo->id }}">
                <input type="hidden" name="visibilita_target_type" value="App\Models\Individuo">
                <input type="hidden" name="redirect_to" value="{{ url('/individui/' . $individuo->id) }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="nome_file" class="form-control form-control-sm" placeholder="Nome documento" required>
                    </div>
                    <div class="col-md-3">
                        <select name="tipologia" class="form-control form-control-sm" required>
                            <option value="">Tipologia...</option>
                            <option value="documento">Documento</option>
                            <option value="avatar">Avatar</option>
                            <option value="galleria">Galleria</option>
                            <option value="statuto">Statuto</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-upload mr-1"></i>Carica</button>
                        <button type="button" class="btn btn-xs btn-secondary" onclick="hideDocumentoForm()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </form>
        </div>
        @if($individuo->documenti->count() > 0)
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
                @foreach($individuo->documenti as $documento)
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
                            <button type="button" class="btn btn-xs btn-primary" onclick="previewDocumento({{ $documento->id }}, '{{ $documento->mime_type }}')" title="Anteprima">
                                <i class="fas fa-eye"></i>
                            </button>
                        @endif
                        <form action="{{ url('/documenti/' . $documento->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <input type="hidden" name="_redirect" value="{{ url('/individui/' . $individuo->id) }}">
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

<div class="mt-3">
    <a href="{{ url('/individui/' . $individuo->id . '/edit') }}" class="btn btn-warning">
        <i class="fas fa-edit mr-1"></i> Modifica
    </a>
    <form action="{{ url('/individui/' . $individuo->id) }}" method="POST" class="d-inline">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Confermi l\'eliminazione di {{ $individuo->nome_completo }}?')">
            <i class="fas fa-trash mr-1"></i> Elimina
        </button>
    </form>
    <a href="{{ route('individui.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Torna all'elenco
    </a>
</div>

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
@endsection

@section('scripts')
<script>
function editContattoInline(id) {
    document.getElementById('contatto-row-' + id).style.display = 'none';
    document.getElementById('contatto-edit-' + id).style.display = 'table-row';
}

function cancelEditContatto(id) {
    document.getElementById('contatto-row-' + id).style.display = 'table-row';
    document.getElementById('contatto-edit-' + id).style.display = 'none';
}

function showGruppoForm() {
    var card = document.getElementById('gruppo-add-card');
    if (card) {
        card.style.display = 'block';
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function hideGruppoForm() {
    var card = document.getElementById('gruppo-add-card');
    if (card) {
        card.style.display = 'none';
    }
}

function submitGruppoForm() {
    var gruppoId = document.getElementById('new-gruppo-id').value;
    var ruolo = document.getElementById('new-gruppo-ruolo').value;
    var dataAdesione = document.getElementById('new-gruppo-data').value;
    
    if (!gruppoId) {
        alert('Seleziona un gruppo');
        return;
    }
    
    var formData = new URLSearchParams();
    formData.append('gruppo_id', gruppoId);
    formData.append('ruolo_nel_gruppo', ruolo);
    formData.append('data_adesione', dataAdesione);
    
    fetch('{{ url('/individui/' . $individuo->id . '/gruppi') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            hideGruppoForm();
            window.location.reload();
        } else {
            alert(data.error || 'Errore');
        }
    })
    .catch(error => {
        alert('Errore: ' + error.message);
    });
}

function editGruppoInline(id) {
    document.getElementById('gruppo-row-' + id).style.display = 'none';
    document.getElementById('gruppo-edit-' + id).style.display = 'table-row';
}

function cancelEditGruppo(id) {
    document.getElementById('gruppo-row-' + id).style.display = 'table-row';
    document.getElementById('gruppo-edit-' + id).style.display = 'none';
}

function deleteGruppo(gruppoId) {
    if (!confirm('Rimuovere da questo gruppo?')) return;
    
    fetch('{{ url('/individui/' . $individuo->id . '/gruppi') }}/' + gruppoId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Errore');
        }
    })
    .catch(error => {
        alert('Errore: ' + error.message);
    });
}

function showDocumentoForm() {
    document.getElementById('documento-add-form').style.display = 'block';
}

function hideDocumentoForm() {
    document.getElementById('documento-add-form').style.display = 'none';
}

function previewDocumento(id, mimeType) {
    var previewUrl = '{{ url('/documenti/') }}/' + id + '/preview';
    var downloadUrl = '{{ url('/documenti/') }}/' + id + '/download';

    document.getElementById('previewFrame').src = previewUrl;
    document.getElementById('previewDownloadBtn').href = downloadUrl;

    if (mimeType && mimeType.startsWith('image/')) {
        document.getElementById('previewDownloadBtn').style.display = 'inline-block';
    }

    $('#previewModal').modal('show');
}
</script>
@endsection
