@extends('layouts.adminlte')
@section('title', 'Invio Mailing')
@section('page_title', 'Invio Mailing')

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane mr-2"></i>Composizione Messaggio</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('mailing.invio.elabora') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Liste Destinatari *</label>
                        <select name="liste[]" id="liste" class="form-control" multiple required size="4">
                            @forelse($liste as $lista)
                            <option value="{{ $lista->id }}">{{ $lista->nome }} ({{ $lista->contatti->count() }} contatti)</option>
                            @empty
                            <option value="">Nessuna lista disponibile</option>
                            @endforelse
                        </select>
                        <small class="text-muted">Seleziona una o più liste (tieni premuto Ctrl per selezioni multiple)</small>
                    </div>

                    <div class="form-group">
                        <label for="oggetto">Oggetto *</label>
                        <input type="text" name="oggetto" id="oggetto" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="corpo">Messaggio *</label>
                        <textarea name="corpo" id="corpo" class="form-control" rows="12" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Allegati</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#documentiModal">
                                    <i class="fas fa-folder-open mr-1"></i> Seleziona da Documenti
                                </button>
                                <label class="btn btn-sm btn-success mb-0">
                                    <i class="fas fa-upload mr-1"></i> Carica nuovo file
                                    <input type="file" name="allegato" id="allegato" class="d-none" onchange="handleFileUpload(this)">
                                </label>
                            </div>
                            <div id="allegati-list"></div>
                            <input type="hidden" name="documenti_selezionati" id="documenti_selezionati" value="">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Prepara Invio
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-folder-open mr-2"></i>Seleziona Documenti</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($documenti->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Nome File</th>
                                <th>Tipologia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documenti as $doc)
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input doc-checkbox" id="doc-{{ $doc->id }}" value="{{ $doc->id }}" data-nome="{{ $doc->nome_file }}">
                                        <label class="custom-control-label" for="doc-{{ $doc->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $doc->nome_file }}</td>
                                <td><span class="badge badge-info">{{ $doc->tipologia ?: $doc->tipo }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">Nessun documento disponibile.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="aggiungiDocumenti()">
                    <i class="fas fa-plus mr-1"></i> Aggiungi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allegatiSelezionati = [];

function aggiungiDocumenti() {
    const checkboxes = document.querySelectorAll('.doc-checkbox:checked');
    checkboxes.forEach(function(cb) {
        const id = parseInt(cb.value);
        const nome = cb.dataset.nome;

        if (!allegatiSelezionati.includes(id)) {
            allegatiSelezionati.push(id);

            const container = document.getElementById('allegati-list');
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center justify-content-between bg-white p-2 mb-2 rounded';
            div.id = 'allegato-' + id;
            div.innerHTML = '<span><i class="fas fa-file mr-2"></i>' + nome + '</span>' +
                '<button type="button" class="btn btn-xs btn-danger" onclick="removeAllegato(' + id + ')">' +
                '<i class="fas fa-times"></i></button>';
            container.appendChild(div);
        }

        cb.checked = false;
    });

    document.getElementById('documenti_selezionati').value = allegatiSelezionati.join(',');
    $('#documentiModal').modal('hide');
}

function removeAllegato(id) {
    allegatiSelezionati = allegatiSelezionati.filter(function(a) { return a !== id; });
    const el = document.getElementById('allegato-' + id);
    if (el) el.remove();
    document.getElementById('documenti_selezionati').value = allegatiSelezionati.join(',');
}

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const container = document.getElementById('allegati-list');
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center justify-content-between bg-white p-2 mb-2 rounded';
        div.innerHTML = '<span><i class="fas fa-file mr-2"></i>' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</span>' +
            '<button type="button" class="btn btn-xs btn-danger" onclick="this.parentElement.remove(); input.value=\'\';">' +
            '<i class="fas fa-times"></i></button>';
        container.appendChild(div);
    }
}
</script>
@endsection