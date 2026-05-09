@extends('layouts.adminlte')
@section('title', 'Nuovo Individuo')
@section('page_title', 'Nuovo Individuo')

@section('content')
<form action="{{ url('/individui') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2"></i>Dati Anagrafici</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Codice ID</label>
                        <input type="text" class="form-control" value="sarà generato automaticamente" disabled>
                    </div>
                    <div class="form-group">
                        <label>Cognome *</label>
                        <input type="text" name="cognome" class="form-control @error('cognome') is-invalid @enderror" value="{{ old('cognome') }}" required>
                        @error('cognome')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required>
                        @error('nome')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Data di nascita</label>
                        <input type="date" name="data_nascita" class="form-control" value="{{ old('data_nascita') }}">
                    </div>
                    <div class="form-group">
                        <label>Genere</label>
                        <select name="genere" class="form-control">
                            <option value="">Seleziona...</option>
                            <option value="M" {{ old('genere') === 'M' ? 'selected' : '' }}>Maschio</option>
                            <option value="F" {{ old('genere') === 'F' ? 'selected' : '' }}>Femmina</option>
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
                        <input type="text" name="indirizzo" class="form-control" value="{{ old('indirizzo') }}">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>CAP</label>
                            <input type="text" name="cap" class="form-control" value="{{ old('cap') }}">
                        </div>
                        <div class="form-group col-md-5">
                            <label>Città</label>
                            <input type="text" name="città" class="form-control" value="{{ old('città') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Provincia</label>
                            <input type="text" name="sigla_provincia" class="form-control" maxlength="2" value="{{ old('sigla_provincia') }}">
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
                            <option value="carta_identita" {{ old('tipo_documento') === 'carta_identita' ? 'selected' : '' }}>Carta d'Identità</option>
                            <option value="patente" {{ old('tipo_documento') === 'patente' ? 'selected' : '' }}>Patente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Numero documento</label>
                        <input type="text" name="numero_documento" class="form-control" value="{{ old('numero_documento') }}">
                    </div>
                    <div class="form-group">
                        <label>Scadenza documento</label>
                        <input type="date" name="scadenza_documento" class="form-control" value="{{ old('scadenza_documento') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Note</h3></div>
                <div class="card-body">
                <div class="form-group mb-0">
                        <textarea name="note" class="form-control" rows="4">{{ old('note') }}</textarea>
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
                </tbody>
            </table>
            <div id="no-contatti-msg" class="text-center text-muted py-4">
                <p class="mb-0">Nessun contatto. Clicca su "Aggiungi" per aggiungerne uno.</p>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Salva
        </button>
        <a href="{{ url('/individui') }}" class="btn btn-secondary">
            <i class="fas fa-times mr-1"></i> Annulla
        </a>
    </div>
</form>
@endsection

@section('scripts')
<script>
let contattoIndex = 0;

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
        <td><button type="button" class="btn btn-danger btn-xs" onclick="rimuoviRiga(this)"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    contattoIndex++;
}

function rimuoviRiga(btn) {
    const row = btn.closest('tr');
    row.remove();
    if (document.getElementById('contatti-tbody').children.length === 0) {
        const noMsg = document.getElementById('no-contatti-msg');
        if (noMsg) noMsg.style.display = 'block';
    }
}
</script>
@endsection
