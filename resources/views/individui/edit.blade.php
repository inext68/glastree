@extends('layouts.adminlte')
@section('title', 'Modifica Individuo')
@section('page_title', 'Modifica Individuo')

@section('content')
<form action="{{ url('/individui/' . $individuo->id) }}" method="POST" id="main-form">
    @csrf @method('PUT')
    <input type="hidden" name="individuo_id" value="{{ $individuo->id }}">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2"></i>Dati Anagrafici</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Codice ID</label>
                        <input type="text" class="form-control" value="{{ $individuo->codice_id }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Cognome *</label>
                        <input type="text" name="cognome" class="form-control" value="{{ $individuo->cognome }}" required>
                    </div>
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="nome" class="form-control" value="{{ $individuo->nome }}" required>
                    </div>
                    <div class="form-group">
                        <label>Data di nascita</label>
                        <input type="date" name="data_nascita" class="form-control" value="{{ $individuo->data_nascita?->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Genere</label>
                        <select name="genere" class="form-control">
                            <option value="">Seleziona...</option>
                            <option value="M" {{ $individuo->genere === 'M' ? 'selected' : '' }}>Maschio</option>
                            <option value="F" {{ $individuo->genere === 'F' ? 'selected' : '' }}>Femmina</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Residenza</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Indirizzo</label>
                        <input type="text" name="indirizzo" class="form-control" value="{{ $individuo->indirizzo }}">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>CAP</label>
                            <input type="text" name="cap" class="form-control" value="{{ $individuo->cap }}">
                        </div>
                        <div class="form-group col-md-5">
                            <label>Città</label>
                            <input type="text" name="città" class="form-control" value="{{ $individuo->città }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Provincia</label>
                            <input type="text" name="sigla_provincia" class="form-control" maxlength="2" value="{{ $individuo->sigla_provincia }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-id-card mr-2"></i>Documento di identità</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo documento</label>
                        <select name="tipo_documento" class="form-control">
                            <option value="">Seleziona...</option>
                            <option value="carta_identita" {{ $individuo->tipo_documento === 'carta_identita' ? 'selected' : '' }}>Carta d'Identità</option>
                            <option value="patente" {{ $individuo->tipo_documento === 'patente' ? 'selected' : '' }}>Patente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Numero documento</label>
                        <input type="text" name="numero_documento" class="form-control" value="{{ $individuo->numero_documento }}">
                    </div>
                    <div class="form-group">
                        <label>Scadenza documento</label>
                        <input type="date" name="scadenza_documento" class="form-control" value="{{ $individuo->scadenza_documento?->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Note</h3></div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <textarea name="note" class="form-control" rows="4">{{ $individuo->note }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-address-book mr-2"></i>Contatti</h3>
            <button type="button" class="btn btn-xs btn-success float-right" onclick="aggiungiRigaContatto()">
                <i class="fas fa-plus mr-1"></i> Aggiungi
            </button>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0" id="contatti-table">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 20%;">Tipo</th>
                        <th style="width: 30%;">Valore</th>
                        <th style="width: 20%;">Etichetta</th>
                        <th style="width: 10%;">Primario</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="contatti-tbody">
                    @foreach($individuo->contatti as $contatto)
                    <tr>
                        <td>
                            <select name="contatti[{{ $loop->index }}][tipo]" class="form-control">
                                <option value="telefono" {{ $contatto->tipo === 'telefono' ? 'selected' : '' }}>Telefono</option>
                                <option value="cellulare" {{ $contatto->tipo === 'cellulare' ? 'selected' : '' }}>Cellulare</option>
                                <option value="email" {{ $contatto->tipo === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="fax" {{ $contatto->tipo === 'fax' ? 'selected' : '' }}>Fax</option>
                                <option value="web" {{ $contatto->tipo === 'web' ? 'selected' : '' }}>Web</option>
                                <option value="telegram" {{ $contatto->tipo === 'telegram' ? 'selected' : '' }}>Telegram</option>
                                <option value="whatsapp" {{ $contatto->tipo === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="altro" {{ $contatto->tipo === 'altro' ? 'selected' : '' }}>Altro</option>
                            </select>
                        </td>
                        <td><input type="text" name="contatti[{{ $loop->index }}][valore]" class="form-control" value="{{ $contatto->valore }}"></td>
                        <td><input type="text" name="contatti[{{ $loop->index }}][etichetta]" class="form-control" value="{{ $contatto->etichetta }}"></td>
                        <td class="text-center"><input type="checkbox" name="contatti[{{ $loop->index }}][is_primary]" value="1" {{ $contatto->is_primary ? 'checked' : '' }}></td>
                        <td><button type="button" class="btn btn-danger btn-xs" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($individuo->contatti->isEmpty())
            <div id="no-contatti-msg" class="text-center text-muted py-4">
                <p class="mb-0">Nessun contatto. Clicca su "Aggiungi" per aggiungerne uno.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Salva Modifiche
        </button>
        <a href="{{ url('/individui/' . $individuo->id) }}" class="btn btn-secondary">
            <i class="fas fa-times mr-1"></i> Annulla
        </a>
    </div>
</form>

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
                        <td>{{ $gruppo->pivot->ruolo_nel_gruppo ?: '-' }}</td>
                        <td>{{ $gruppo->pivot->data_adesione ? \Carbon\Carbon::parse($gruppo->pivot->data_adesione)->format('d/m/Y') : '-' }}</td>
                        <td>
                            <button type="button" class="btn btn-xs btn-warning" onclick="editGruppoInline({{ $gruppo->id }})" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ url('/individui/' . $individuo->id . '/gruppi/' . $gruppo->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Rimuovere da questo gruppo?')" title="Rimuovi">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr id="gruppo-edit-{{ $gruppo->id }}" style="display:none;">
                        <td colspan="5">
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
let contattoIndex = {{ $individuo->contatti->count() }};

function aggiungiRigaContatto() {
    const tbody = document.getElementById('contatti-tbody');
    const noMsg = document.getElementById('no-contatti-msg');
    if (noMsg) noMsg.style.display = 'none';

    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="contatti[${contattoIndex}][tipo]" class="form-control">
                <option value="">Seleziona...</option>
                <option value="telefono">Telefono</option>
                <option value="cellulare">Cellulare</option>
                <option value="email">Email</option>
                <option value="fax">Fax</option>
                <option value="web">Web</option>
                <option value="telegram">Telegram</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="altro">Altro</option>
            </select>
        </td>
        <td><input type="text" name="contatti[${contattoIndex}][valore]" class="form-control" placeholder="Valore"></td>
        <td><input type="text" name="contatti[${contattoIndex}][etichetta]" class="form-control" placeholder="Etichetta"></td>
        <td class="text-center"><input type="checkbox" name="contatti[${contattoIndex}][is_primary]" value="1"></td>
        <td><button type="button" class="btn btn-danger btn-xs" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    contattoIndex++;
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
