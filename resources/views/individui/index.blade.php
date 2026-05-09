@extends('layouts.adminlte')
@section('title', 'Individui')
@section('page_title', 'Elenco Individui')

@php
$tableColumns = [
    ['key' => 'codice', 'label' => 'Codice', 'visible' => true],
    ['key' => 'cognome', 'label' => 'Cognome', 'visible' => true],
    ['key' => 'nome', 'label' => 'Nome', 'visible' => true],
    ['key' => 'email', 'label' => 'Email', 'visible' => true],
    ['key' => 'telefono', 'label' => 'Telefono', 'visible' => true],
    ['key' => 'azioni', 'label' => 'Azioni', 'visible' => true],
];

$vistaDefaultJson = $vista ? $vista->toJson() : 'null';
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Lista Individui</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-info btn-sm mr-1" onclick="toggleSearchPanel()">
                <i class="fas fa-search mr-1"></i> Ricerca/Filtri
            </button>
            <div class="btn-group mr-1">
                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-cog mr-1"></i> Azioni
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="exportCSV(); return false;">
                        <i class="fas fa-file-csv mr-2"></i>Esporta Tutti CSV
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportSelectedCSV(); return false;">
                        <i class="fas fa-file-csv mr-2"></i>Esporta Selezionati CSV
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="printSelected(); return false;">
                        <i class="fas fa-print mr-2"></i>Stampa Selezionati
                    </a>
                    <a class="dropdown-item" href="#" onclick="emailSelected(); return false;">
                        <i class="fas fa-envelope mr-2"></i>Email Selezionati
                    </a>
                    <a class="dropdown-item" href="#" onclick="showCreateMailingListModal(); return false;">
                        <i class="fas fa-list mr-2"></i>Crea Mailing List
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteSelected(); return false;">
                        <i class="fas fa-trash mr-2"></i>Elimina Selezionati
                    </a>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="showSaveVistaModal()">
                <i class="fas fa-save mr-1"></i> Salva Vista
            </button>
            <a href="{{ route('individui.import') }}" class="btn btn-warning btn-sm ml-2">
                <i class="fas fa-file-import mr-1"></i> Importa
            </a>
            <a href="{{ url('/individui/create') }}" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-plus mr-1"></i> Nuovo
            </a>
        </div>
    </div>

    <div id="search-panel" class="p-3 bg-light border-bottom" style="display: none;">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Cerca</label>
                    <input type="text" id="global-search" class="form-control form-control-sm" placeholder="Cerca in tutte le colonne..." oninput="applyGlobalSearch(this.value)">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Colonna</label>
                    <select id="filter-column" class="form-control form-control-sm">
                        <option value="">Tutte le colonne</option>
                        @foreach($tableColumns as $col)
                        <option value="{{ $col['key'] }}">{{ $col['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Operatore</label>
                    <select id="filter-operator" class="form-control form-control-sm">
                        <option value="contains">Contiene</option>
                        <option value="equals">Uguale a</option>
                        <option value="starts">Inizia con</option>
                        <option value="ends">Finisce con</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Valore</label>
                    <input type="text" id="filter-value" class="form-control form-control-sm" placeholder="Valore filtro">
                </div>
            </div>
            <div class="col-md-1">
                <label class="small">&nbsp;</label>
                <div>
                    <button type="button" class="btn btn-xs btn-primary" onclick="applyColumnFilter()">
                        <i class="fas fa-filter"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-secondary" onclick="clearFilters()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Ordina cliccando sulle intestazioni delle colonne
            </small>
        </div>
    </div>

    <div class="card-body p-0">
        @if($individui->count() > 0)
        <table class="table table-bordered table-hover table-striped mb-0" id="individui-table">
            <thead class="thead-light">
                <tr>
                    <th style="width: 40px;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="select-all" onchange="toggleSelectAll(this)">
                            <label class="custom-control-label" for="select-all"></label>
                        </div>
                    </th>
                    <th style="width: 100px;" data-sortable="true" data-column="codice" onclick="sortTable('codice')">Codice <i class="fas fa-sort float-right"></i></th>
                    <th data-sortable="true" data-column="cognome" onclick="sortTable('cognome')">Cognome <i class="fas fa-sort float-right"></i></th>
                    <th data-sortable="true" data-column="nome" onclick="sortTable('nome')">Nome <i class="fas fa-sort float-right"></i></th>
                    <th data-sortable="true" data-column="email" onclick="sortTable('email')">Email <i class="fas fa-sort float-right"></i></th>
                    <th data-sortable="true" data-column="telefono" onclick="sortTable('telefono')">Telefono <i class="fas fa-sort float-right"></i></th>
                    <th style="width: 120px;">Azioni</th>
                </tr>
            </thead>
<tbody id="table-body">
                @foreach($individui as $ind)
                <tr data-id="{{ $ind->id }}">
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input row-checkbox" id="select-{{ $ind->id }}" value="{{ $ind->id }}">
                            <label class="custom-control-label" for="select-{{ $ind->id }}"></label>
                        </div>
                    </td>
                    <td><span class="badge badge-secondary">{{ $ind->codice_id }}</span></td>
                    <td class="font-weight-bold">
                        <a href="{{ url('/individui/' . $ind->id . '/edit') }}">{{ $ind->cognome }}</a>
                    </td>
                    <td>
                        <a href="{{ url('/individui/' . $ind->id . '/edit') }}">{{ $ind->nome }}</a>
                    </td>
                    <td>{{ $ind->getEmailPrimariaAttribute() ?: '-' }}</td>
                    <td>{{ $ind->getTelefonoPrimarioAttribute() ?: '-' }}</td>
                    <td>
                        <a href="{{ url('/individui/' . $ind->id . '/edit') }}" class="btn btn-xs btn-warning" title="Modifica">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-xs btn-danger" onclick="showDeleteModal({{ $ind->id }}, '{{ $ind->cognome }} {{ $ind->nome }}')" title="Elimina">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-secondary" onclick="printIndividuo({{ $ind->id }})" title="Stampa">
                            <i class="fas fa-print"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-primary" onclick="exportIndividuoCSV({{ $ind->id }})" title="Esporta CSV">
                            <i class="fas fa-download"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-info" onclick="sendEmail({{ $ind->id }})" title="Invia Email">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-dark" onclick="generateReport({{ $ind->id }})" title="Genera Report">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center text-muted py-5">
            <i class="fas fa-users fa-3x mb-3"></i>
            <p>Nessun individuo presente</p>
            <a href="{{ url('/individui/create') }}" class="btn btn-success">Crea il primo individuo</a>
        </div>
        @endif
    </div>
    @if($individui->count() > 0)
    <div class="card-footer">
        {{ $individui->links() }}
    </div>
    @endif
</div>

<div class="modal fade" id="saveVistaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-save mr-2"></i>Salva Vista</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nome Vista *</label>
                    <input type="text" id="vista-nome" class="form-control" placeholder="Es. Elenco soci attivi">
                </div>
                <div class="form-group">
                    <label>Colonne visibili</label>
                    <div class="row" id="colonne-checkboxes">
                        @foreach($tableColumns as $col)
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input col-visibile" id="col-vis-{{ $col['key'] }}" value="{{ $col['key'] }}" checked>
                                <label class="custom-control-label" for="col-vis-{{ $col['key'] }}">{{ $col['label'] }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="vista-default" value="true">
                        <label class="custom-control-label" for="vista-default">Imposta come vista predefinita</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="saveVista()">Salva</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="colonneModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-columns mr-2"></i>Colonne Visibili</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($tableColumns as $col)
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input col-toggle" id="col-toggle-{{ $col['key'] }}" value="{{ $col['key'] }}" checked onchange="toggleColumn('{{ $col['key'] }}', this.checked)">
                            <label class="custom-control-label" for="col-toggle-{{ $col['key'] }}">{{ $col['label'] }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><i class="fas fa-trash-alt mr-2"></i>Conferma Eliminazione</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Stai per eliminare l'individuo: <strong id="delete-individuo-name"></strong></p>
                <p class="text-muted">Seleziona gli elementi collegati da eliminare:</p>
                <div class="ml-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="delete-contatti" checked>
                        <label class="custom-control-label" for="delete-contatti">Contatti (<span id="count-contatti">0</span>)</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="delete-gruppi">
                        <label class="custom-control-label" for="delete-gruppi">Gruppi (<span id="count-gruppi">0</span>) - solo scollegamento</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="delete-documenti" checked>
                        <label class="custom-control-label" for="delete-documenti">Documenti (<span id="count-documenti">0</span>)</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="delete-eventi">
                        <label class="custom-control-label" for="delete-eventi">Eventi (<span id="count-eventi">0</span>) - solo scollegamento</label>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <small><i class="fas fa-info-circle mr-1"></i>Se non selezionato, l'elemento verrà scollegato ma non eliminato.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn" onclick="confirmDelete()">Elimina</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createMailingListModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white"><i class="fas fa-list mr-2"></i>Crea Mailing List</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nome Lista *</label>
                    <input type="text" id="ml-nome" class="form-control" placeholder="Es. Newsletter Maggio 2026">
                </div>
                <div class="form-group">
                    <label>Descrizione</label>
                    <textarea id="ml-descrizione" class="form-control" rows="2" placeholder="Descrizione opzionale"></textarea>
                </div>

                <div class="form-group">
                    <label>Contatti inclusi (<span id="ml-count">0</span>)</label>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" id="ml-contatti-table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="ml-select-all" onchange="toggleMlSelectAll(this)">
                                            <label class="custom-control-label" for="ml-select-all"></label>
                                        </div>
                                    </th>
                                    <th>Codice</th>
                                    <th>Cognome</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="ml-contatti-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" onclick="createMailingList()">
                    <i class="fas fa-save mr-1"></i> Salva Lista
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentSort = { column: null, direction: 'asc' };
let columnVisibility = {!! json_encode(array_column($tableColumns, 'key')) !!};

function toggleSearchPanel() {
    const panel = document.getElementById('search-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function applyGlobalSearch(value) {
    const rows = document.querySelectorAll('#table-body tr');
    const searchTerm = value.toLowerCase();
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function applyColumnFilter() {
    const col = document.getElementById('filter-column').value;
    const op = document.getElementById('filter-operator').value;
    const val = document.getElementById('filter-value').value.toLowerCase();
    
    if (!col || !val) return;
    
    const rows = document.querySelectorAll('#table-body tr');
    const colIndex = { codice: 1, cognome: 2, nome: 3, email: 4, telefono: 5, azioni: 6 }[col];
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const cellText = cells[colIndex]?.textContent.toLowerCase() || '';
        let matches = false;
        
        switch(op) {
            case 'contains': matches = cellText.includes(val); break;
            case 'equals': matches = cellText === val; break;
            case 'starts': matches = cellText.startsWith(val); break;
            case 'ends': matches = cellText.endsWith(val); break;
        }
        
        row.style.display = matches ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('global-search').value = '';
    document.getElementById('filter-column').value = '';
    document.getElementById('filter-value').value = '';
    document.querySelectorAll('#table-body tr').forEach(row => row.style.display = '');
}

function sortTable(column) {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort = { column: column, direction: 'asc' };
    }
    
    const colIndex = { codice: 0, cognome: 1, nome: 2, email: 3, telefono: 4, azioni: 5 }[column];
    const tbody = document.getElementById('table-body');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aVal = a.querySelectorAll('td')[colIndex]?.textContent.trim() || '';
        const bVal = b.querySelectorAll('td')[colIndex]?.textContent.trim() || '';
        const cmp = aVal.localeCompare(bVal, undefined, { numeric: true });
        return currentSort.direction === 'asc' ? cmp : -cmp;
    });
    
    rows.forEach(row => tbody.appendChild(row));
    
    document.querySelectorAll('th[data-sortable]').forEach(th => {
        const icon = th.querySelector('i');
        if (th.dataset.column === column) {
            icon.className = currentSort.direction === 'asc' ? 'fas fa-sort-up float-right' : 'fas fa-sort-down float-right';
        } else {
            icon.className = 'fas fa-sort float-right';
        }
    });
}

function toggleColumn(column, visible) {
    const colIndex = { codice: 1, cognome: 2, nome: 3, email: 4, telefono: 5, azioni: 6 }[column];
    const ths = document.querySelectorAll('#individui-table thead th');
    const rows = document.querySelectorAll('#table-body tr');

    if (visible) {
        ths[colIndex].style.display = '';
        rows.forEach(row => {
            row.querySelectorAll('td')[colIndex].style.display = '';
        });
    } else {
        ths[colIndex].style.display = 'none';
        rows.forEach(row => {
            row.querySelectorAll('td')[colIndex].style.display = 'none';
        });
}
}

async function saveVista() {
    let nome = document.getElementById('vista-nome').value.trim();
    if (!nome) {
        const now = new Date();
        nome = 'Vista ' + now.toLocaleDateString('it-IT') + ' ' + now.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
    }

    const colonneVisibili = Array.from(document.querySelectorAll('#colonne-checkboxes input:checked')).map(i => i.value);
    const isDefault = document.getElementById('vista-default').checked;

    const data = {
        nome: nome,
        tipo: 'individui',
        colonne_visibili: colonneVisibili,
        colonne_ordinamento: currentSort.column ? [[currentSort.column, currentSort.direction]] : [],
        ricerca: document.getElementById('global-search').value,
        is_default: isDefault
    };

    try {
        const response = await fetch('/glastree/viste', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Vista salvata!');
            $('#saveVistaModal').modal('hide');
        } else {
            alert('Errore salvataggio');
        }
    } catch(e) {
        alert('Errore: ' + e.message);
    }
}

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
}

function exportCSV() {
    const rows = [];
    const headers = ['Codice', 'Cognome', 'Nome', 'Email', 'Telefono'];
    rows.push(headers.join(','));

    document.querySelectorAll('#table-body tr:visible').forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).slice(1, 6);
        const values = cells.map(cell => `"${cell.textContent.trim().replace(/"/g, '""')}"`);
        rows.push(values.join(','));
    });
    
    const blob = new Blob([rows.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `individui_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
}

let deleteIndividuoId = null;

async function showDeleteModal(id, nome) {
    deleteIndividuoId = id;
    document.getElementById('delete-individuo-name').textContent = nome;

    document.getElementById('count-contatti').textContent = '...';
    document.getElementById('count-gruppi').textContent = '...';
    document.getElementById('count-documenti').textContent = '...';
    document.getElementById('count-eventi').textContent = '...';

    try {
        const response = await fetch(`/glastree/individui/${id}/collegati`);
        const data = await response.json();
        document.getElementById('count-contatti').textContent = data.contatti;
        document.getElementById('count-gruppi').textContent = data.gruppi;
        document.getElementById('count-documenti').textContent = data.documenti;
        document.getElementById('count-eventi').textContent = data.eventi;
    } catch(e) {
        console.error('Errore recupero dati:', e);
    }

    $('#deleteModal').modal('show');
}

function confirmDelete() {
    const eliminaContatti = document.getElementById('delete-contatti').checked;
    const eliminaDocumenti = document.getElementById('delete-documenti').checked;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/glastree/individui/${deleteIndividuoId}/elimina`;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="elimina_contatti" value="${eliminaContatti}">
        <input type="hidden" name="elimina_documenti" value="${eliminaDocumenti}">
    `;

    document.body.appendChild(form);
    form.submit();
}

function printIndividuo(id) {
    window.open(`/glastree/individui/${id}`, '_blank');
}

function exportIndividuoCSV(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) return;

    const cells = Array.from(row.querySelectorAll('td'));
    const data = {
        codice: cells[0].textContent.trim(),
        cognome: cells[1].textContent.trim(),
        nome: cells[2].textContent.trim(),
        email: cells[3].textContent.trim(),
        telefono: cells[4].textContent.trim()
    };

    const csv = `Codice,Cognome,Nome,Email,Telefono\n"${data.codice}","${data.cognome}","${data.nome}","${data.email}","${data.telefono}"`;
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `individuo_${data.codice}.csv`;
    a.click();
}

function sendEmail(id) {
    window.location.href = `/glastree/mailing/nuovo?individui=${id}`;
}

function generateReport(id) {
    window.open('/glastree/individui/' + id + '/report', '_blank');
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
}

function exportSelectedCSV() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Seleziona almeno un individuo');
        return;
    }

    const rows = [];
    rows.push('Codice,Cognome,Nome,Email,Telefono');

    document.querySelectorAll('#table-body tr').forEach(row => {
        const checkbox = row.querySelector('.row-checkbox');
        if (checkbox && checkbox.checked) {
            const cells = Array.from(row.querySelectorAll('td')).slice(1, 6);
            const values = cells.map(cell => `"${cell.textContent.trim().replace(/"/g, '""')}"`);
            rows.push(values.join(','));
        }
    });

    const blob = new Blob([rows.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `individui_selezionati_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
}

function printSelected() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Seleziona almeno un individuo');
        return;
    }
    const firstId = selectedIds[0];
    window.open(`/glastree/individui/${firstId}`, '_blank');
}

function emailSelected() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Seleziona almeno un individuo');
        return;
    }
    window.location.href = `/glastree/mailing/nuovo?individui=${selectedIds.join(',')}`;
}

function deleteSelected() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Seleziona almeno un individuo');
        return;
    }

    document.getElementById('delete-individuo-name').textContent = selectedIds.length + ' individui selezionati';
    document.getElementById('count-contatti').textContent = '...';
    document.getElementById('count-gruppi').textContent = '...';
    document.getElementById('count-documenti').textContent = '...';
    document.getElementById('count-eventi').textContent = '...';

    document.getElementById('confirm-delete-btn').onclick = function() {
        const eliminaContatti = document.getElementById('delete-contatti').checked;
        const eliminaDocumenti = document.getElementById('delete-documenti').checked;

        selectedIds.forEach(function(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/glastree/individui/${id}/elimina`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                <input type="hidden" name="elimina_contatti" value="${eliminaContatti}">
                <input type="hidden" name="elimina_documenti" value="${eliminaDocumenti}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    };

    $('#deleteModal').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }

    const vistaDefault = {!! $vistaDefaultJson !!};
    if (vistaDefault && vistaDefault.colonne_visibili) {
        const colonneVisibili = vistaDefault.colonne_visibili;
        const tutteColonne = ['codice', 'cognome', 'nome', 'email', 'telefono', 'azioni'];
        tutteColonne.forEach(function(col) {
            const visibile = colonneVisibili.includes(col);
            const checkbox = document.querySelector(`#col-vis-${col}`);
            const toggle = document.querySelector(`#col-toggle-${col}`);
            if (checkbox) checkbox.checked = visibile;
            if (toggle) {
                toggle.checked = visibile;
                toggleColumn(col, visibile);
            }
        });
        if (vistaDefault.ricerca) {
            document.getElementById('global-search').value = vistaDefault.ricerca;
            applyGlobalSearch(vistaDefault.ricerca);
        }
    }
});

function showCreateMailingListModal() {
    document.getElementById('ml-nome').value = '';
    document.getElementById('ml-descrizione').value = '';
    document.getElementById('ml-contatti-tbody').innerHTML = '';
    document.getElementById('ml-count').textContent = '0';

    const selectedIds = getSelectedIds();
    console.log('Selected IDs:', selectedIds);
    if (selectedIds.length > 0) {
        const url = '/glastree/individui/email-list?ids=' + selectedIds.join(',');
        console.log('Fetching URL:', url);
        fetch(url, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response type:', response.headers.get('content-type'));
                if (!response.ok) {
                    return response.text().then(text => {
                        console.log('Full error response:', text.substring(0, 500));
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const json = JSON.parse(text);
                            throw new Error(json.message || 'Errore HTTP ' + response.status);
                        } else {
                            throw new Error('Server returned HTML (status ' + response.status + ') - probabilmente sessione scaduta. Ricarica la pagina.');
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Data:', data);
                updateMlTable(data);
            })
            .catch(function(err) {
                console.error('Error:', err);
                alert('Errore caricamento contatti: ' + err.message + '\nURL: ' + url);
            });
    } else {
        alert('Seleziona almeno un individuo dalla tabella');
    }

    $('#createMailingListModal').modal('show');
}

function updateMlTable(data) {
    const tbody = document.getElementById('ml-contatti-tbody');
    const existingIds = new Set();

    document.querySelectorAll('.ml-row-checkbox').forEach(function(cb) {
        existingIds.add(parseInt(cb.value));
    });

    data.forEach(function(item) {
        if (existingIds.has(item.id)) return;

        const tr = document.createElement('tr');
        const checked = item.email_count === 1 ? 'checked' : '';
        tr.innerHTML = `
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input ml-row-checkbox" id="ml-${item.id}" value="${item.id}" data-email="${item.email || ''}" ${checked}>
                    <label class="custom-control-label" for="ml-${item.id}"></label>
                </div>
            </td>
            <td><span class="badge badge-secondary">${item.codice_id}</span></td>
             <td>${item.cognome}</td>
            <td>${item.nome}</td>
            <td>
                ${item.email_count === 1 ? (item.email || '-') :
                  '<select class="form-control form-control-sm" onchange="updateMlCheckboxEmail(' + item.id + ', this.value)">' +
                  '<option value="">Seleziona email...</option>' +
                  (item.emails || []).map(function(e) { return '<option value="' + e + '">' + e + '</option>'; }).join('') +
                  '</select>'}
            </td>
            <td>
                <button type="button" class="btn btn-xs btn-danger" onclick="this.closest('tr').remove(); updateMlCount();">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    updateMlCount();
}

function updateMlCount() {
    document.getElementById('ml-count').textContent = document.querySelectorAll('.ml-row-checkbox').length;
}

function toggleMlSelectAll(source) {
    document.querySelectorAll('.ml-row-checkbox').forEach(function(cb) {
        cb.checked = source.checked;
    });
}

function updateMlCheckboxEmail(id, email) {
    const checkbox = document.getElementById('ml-' + id);
    if (checkbox) {
        checkbox.dataset.email = email;
        checkbox.checked = email !== '';
    }
}

async function createMailingList() {
    const nome = document.getElementById('ml-nome').value.trim();
    if (!nome) {
        alert('Inserisci un nome per la lista');
        return;
    }

    const selected = [];
    document.querySelectorAll('.ml-row-checkbox:checked').forEach(function(cb) {
        if (cb.dataset.email) {
            selected.push({
                individuo_id: parseInt(cb.value),
                email: cb.dataset.email
            });
        }
    });

    if (selected.length === 0) {
        alert('Seleziona almeno un contatto con email');
        return;
    }

    const data = {
        nome: nome,
        descrizione: document.getElementById('ml-descrizione').value.trim(),
        contatti: selected
    };

    try {
        const response = await fetch('/glastree/mailing-liste/create-from-individui', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Mailing list creata con successo!');
            $('#createMailingListModal').modal('hide');
        } else {
            alert('Errore nella creazione della lista');
        }
    } catch(e) {
        alert('Errore: ' + e.message);
    }
}
</script>
@endsection