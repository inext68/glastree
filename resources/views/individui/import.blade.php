@extends('layouts.adminlte')
@section('title', 'Importa Individui')
@section('page_title', 'Importa Individui da CSV')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-upload mr-2"></i>Carica file CSV</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('individui.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Seleziona file CSV *</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt" required>
                        <small class="form-text text-muted">
                            Il file deve essere in formato CSV con separatore virgola.
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-1"></i> Importa
                    </button>
                    <a href="{{ route('individui.index') }}" class="btn btn-secondary ml-2">
                        Annulla
                    </a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title text-white"><i class="fas fa-download mr-2"></i>Template</h3>
            </div>
            <div class="card-body">
                <p>Scarica il template CSV per l'importazione:</p>
                <a href="{{ route('individui.template') }}" class="btn btn-success btn-block">
                    <i class="fas fa-file-csv mr-1"></i> Scarica Template
                </a>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Formato CSV</h3>
            </div>
            <div class="card-body">
                <h6>Campi obbligatori:</h6>
                <ul>
                    <li><code>cognome</code></li>
                    <li><code>nome</code></li>
                </ul>
                <h6>Campi opzionali:</h6>
                <ul>
                    <li><code>data_nascita</code> (formato YYYY-MM-DD)</li>
                    <li><code>indirizzo</code></li>
                    <li><code>cap</code></li>
                    <li><code>città</code></li>
                    <li><code>sigla_provincia</code> (2 lettere)</li>
                    <li><code>genere</code> (M o F)</li>
                    <li><code>tipo_documento</code> (carta_identita, patente)</li>
                    <li><code>numero_documento</code></li>
                    <li><code>scadenza_documento</code> (YYYY-MM-DD)</li>
                    <li><code>note</code></li>
                </ul>
                <h6>Contatti (fino a 2):</h6>
                <ul>
                    <li><code>contatto_1_tipo</code> (email, telefono, cellulare)</li>
                    <li><code>contatto_1_valore</code></li>
                    <li><code>contatto_1_etichetta</code> (personale, lavoro)</li>
                    <li><code>contatto_2_*</code> (secondo contatto)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection