@extends('layouts.adminlte')
@section('title', 'Modifica Evento')
@section('page_title', 'Modifica Evento')

@section('content')
<form action="{{ url('/eventi/' . $evento->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar mr-2"></i>Dati Evento</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nome Evento *</label>
                        <input type="text" name="nome_evento" class="form-control" value="{{ old('nome_evento', $evento->nome_evento) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Descrizione Breve</label>
                        <input type="text" name="descrizione_evento" class="form-control" value="{{ old('descrizione_evento', $evento->descrizione_evento) }}">
                    </div>
                    <div class="form-group">
                        <label>Descrizione Completa</label>
                        <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione', $evento->descrizione) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_incontro_gruppo" value="1" {{ $evento->is_incontro_gruppo ? 'checked' : '' }}>
                            Questo evento è il giorno di incontro del gruppo
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-clock mr-2"></i>Data e Orario</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo Ricorrenza</label>
                        <select name="tipo_recorrenza" id="tipo_recorrenza" class="form-control" onchange="toggleRicorrenza()">
                            <option value="singolo" {{ old('tipo_recorrenza', $evento->tipo_recorrenza) === 'singolo' || old('tipo_recorrenza', $evento->tipo_recorrenza) === null ? 'selected' : '' }}>Singolo</option>
                            <option value="settimanale" {{ old('tipo_recorrenza', $evento->tipo_recorrenza) === 'settimanale' ? 'selected' : '' }}>Settimanale</option>
                            <option value="mensile" {{ old('tipo_recorrenza', $evento->tipo_recorrenza) === 'mensile' ? 'selected' : '' }}>Mensile</option>
                            <option value="annuale" {{ old('tipo_recorrenza', $evento->tipo_recorrenza) === 'annuale' ? 'selected' : '' }}>Annuale</option>
                            <option value="altro" {{ old('tipo_recorrenza', $evento->tipo_recorrenza) === 'altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                    </div>

                    <div id="data_specifica_field" style="{{ in_array($evento->tipo_recorrenza, [null, 'singolo']) ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Data Evento</label>
                            <input type="date" name="data_specifica" class="form-control" value="{{ old('data_specifica', $evento->data_specifica?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div id="settimanale_field" style="{{ $evento->tipo_recorrenza === 'settimanale' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Giorno della Settimana</label>
                            <select name="giorno_settimana" class="form-control">
                                <option value="">Seleziona...</option>
                                <option value="0" {{ $evento->giorno_settimana == 0 ? 'selected' : '' }}>Domenica</option>
                                <option value="1" {{ $evento->giorno_settimana == 1 ? 'selected' : '' }}>Lunedì</option>
                                <option value="2" {{ $evento->giorno_settimana == 2 ? 'selected' : '' }}>Martedì</option>
                                <option value="3" {{ $evento->giorno_settimana == 3 ? 'selected' : '' }}>Mercoledì</option>
                                <option value="4" {{ $evento->giorno_settimana == 4 ? 'selected' : '' }}>Giovedì</option>
                                <option value="5" {{ $evento->giorno_settimana == 5 ? 'selected' : '' }}>Venerdì</option>
                                <option value="6" {{ $evento->giorno_settimana == 6 ? 'selected' : '' }}>Sabato</option>
                            </select>
                        </div>
                    </div>

                    <div id="mensile_field" style="{{ $evento->tipo_recorrenza === 'mensile' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Occorrenza nel mese</label>
                            @php
                            $occorrenzaSelezionata = old('occorrenza_mese', $evento->occorrenza_mese);
                            @endphp
                            <select name="occorrenza_mese" class="form-control">
                                <option value="">Seleziona...</option>
                                <option value="1,1" {{ $occorrenzaSelezionata === '1,1' ? 'selected' : '' }}>1° Lunedì</option>
                                <option value="1,2" {{ $occorrenzaSelezionata === '1,2' ? 'selected' : '' }}>1° Martedì</option>
                                <option value="1,3" {{ $occorrenzaSelezionata === '1,3' ? 'selected' : '' }}>1° Mercoledì</option>
                                <option value="1,4" {{ $occorrenzaSelezionata === '1,4' ? 'selected' : '' }}>1° Giovedì</option>
                                <option value="1,5" {{ $occorrenzaSelezionata === '1,5' ? 'selected' : '' }}>1° Venerdì</option>
                                <option value="1,6" {{ $occorrenzaSelezionata === '1,6' ? 'selected' : '' }}>1° Sabato</option>
                                <option value="1,0" {{ $occorrenzaSelezionata === '1,0' ? 'selected' : '' }}>1° Domenica</option>
                                <option value="2,1" {{ $occorrenzaSelezionata === '2,1' ? 'selected' : '' }}>2° Lunedì</option>
                                <option value="2,2" {{ $occorrenzaSelezionata === '2,2' ? 'selected' : '' }}>2° Martedì</option>
                                <option value="2,3" {{ $occorrenzaSelezionata === '2,3' ? 'selected' : '' }}>2° Mercoledì</option>
                                <option value="2,4" {{ $occorrenzaSelezionata === '2,4' ? 'selected' : '' }}>2° Giovedì</option>
                                <option value="2,5" {{ $occorrenzaSelezionata === '2,5' ? 'selected' : '' }}>2° Venerdì</option>
                                <option value="2,6" {{ $occorrenzaSelezionata === '2,6' ? 'selected' : '' }}>2° Sabato</option>
                                <option value="2,0" {{ $occorrenzaSelezionata === '2,0' ? 'selected' : '' }}>2° Domenica</option>
                                <option value="3,1" {{ $occorrenzaSelezionata === '3,1' ? 'selected' : '' }}>3° Lunedì</option>
                                <option value="3,2" {{ $occorrenzaSelezionata === '3,2' ? 'selected' : '' }}>3° Martedì</option>
                                <option value="3,3" {{ $occorrenzaSelezionata === '3,3' ? 'selected' : '' }}>3° Mercoledì</option>
                                <option value="3,4" {{ $occorrenzaSelezionata === '3,4' ? 'selected' : '' }}>3° Giovedì</option>
                                <option value="3,5" {{ $occorrenzaSelezionata === '3,5' ? 'selected' : '' }}>3° Venerdì</option>
                                <option value="3,6" {{ $occorrenzaSelezionata === '3,6' ? 'selected' : '' }}>3° Sabato</option>
                                <option value="3,0" {{ $occorrenzaSelezionata === '3,0' ? 'selected' : '' }}>3° Domenica</option>
                                <option value="4,1" {{ $occorrenzaSelezionata === '4,1' ? 'selected' : '' }}>4° Lunedì</option>
                                <option value="4,2" {{ $occorrenzaSelezionata === '4,2' ? 'selected' : '' }}>4° Martedì</option>
                                <option value="4,3" {{ $occorrenzaSelezionata === '4,3' ? 'selected' : '' }}>4° Mercoledì</option>
                                <option value="4,4" {{ $occorrenzaSelezionata === '4,4' ? 'selected' : '' }}>4° Giovedì</option>
                                <option value="4,5" {{ $occorrenzaSelezionata === '4,5' ? 'selected' : '' }}>4° Venerdì</option>
                                <option value="4,6" {{ $occorrenzaSelezionata === '4,6' ? 'selected' : '' }}>4° Sabato</option>
                                <option value="4,0" {{ $occorrenzaSelezionata === '4,0' ? 'selected' : '' }}>4° Domenica</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mesi (multipli)</label>
                            @php
                            $mesiSelezionati = $evento->mesi_recorrenza ? explode(',', $evento->mesi_recorrenza) : [];
                            @endphp
                            <select name="mesi_recorrenza[]" class="form-control" multiple size="4">
                                <option value="1" {{ in_array('1', $mesiSelezionati) ? 'selected' : '' }}>Gennaio</option>
                                <option value="2" {{ in_array('2', $mesiSelezionati) ? 'selected' : '' }}>Febbraio</option>
                                <option value="3" {{ in_array('3', $mesiSelezionati) ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ in_array('4', $mesiSelezionati) ? 'selected' : '' }}>Aprile</option>
                                <option value="5" {{ in_array('5', $mesiSelezionati) ? 'selected' : '' }}>Maggio</option>
                                <option value="6" {{ in_array('6', $mesiSelezionati) ? 'selected' : '' }}>Giugno</option>
                                <option value="7" {{ in_array('7', $mesiSelezionati) ? 'selected' : '' }}>Luglio</option>
                                <option value="8" {{ in_array('8', $mesiSelezionati) ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ in_array('9', $mesiSelezionati) ? 'selected' : '' }}>Settembre</option>
                                <option value="10" {{ in_array('10', $mesiSelezionati) ? 'selected' : '' }}>Ottobre</option>
                                <option value="11" {{ in_array('11', $mesiSelezionati) ? 'selected' : '' }}>Novembre</option>
                                <option value="12" {{ in_array('12', $mesiSelezionati) ? 'selected' : '' }}>Dicembre</option>
                            </select>
                            <small class="form-text text-muted">Ctrl+clic per selezionare più mesi</small>
                        </div>
                    </div>

                    <div id="annuale_field" style="{{ $evento->tipo_recorrenza === 'annuale' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Mese</label>
                            <select name="mese_annuale" class="form-control">
                                <option value="">Seleziona mese...</option>
                                <option value="1" {{ old('mese_annuale', $evento->mese_annuale) == '1' ? 'selected' : '' }}>Gennaio</option>
                                <option value="2" {{ old('mese_annuale', $evento->mese_annuale) == '2' ? 'selected' : '' }}>Febbraio</option>
                                <option value="3" {{ old('mese_annuale', $evento->mese_annuale) == '3' ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ old('mese_annuale', $evento->mese_annuale) == '4' ? 'selected' : '' }}>Aprile</option>
                                <option value="5" {{ old('mese_annuale', $evento->mese_annuale) == '5' ? 'selected' : '' }}>Maggio</option>
                                <option value="6" {{ old('mese_annuale', $evento->mese_annuale) == '6' ? 'selected' : '' }}>Giugno</option>
                                <option value="7" {{ old('mese_annuale', $evento->mese_annuale) == '7' ? 'selected' : '' }}>Luglio</option>
                                <option value="8" {{ old('mese_annuale', $evento->mese_annuale) == '8' ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ old('mese_annuale', $evento->mese_annuale) == '9' ? 'selected' : '' }}>Settembre</option>
                                <option value="10" {{ old('mese_annuale', $evento->mese_annuale) == '10' ? 'selected' : '' }}>Ottobre</option>
                                <option value="11" {{ old('mese_annuale', $evento->mese_annuale) == '11' ? 'selected' : '' }}>Novembre</option>
                                <option value="12" {{ old('mese_annuale', $evento->mese_annuale) == '12' ? 'selected' : '' }}>Dicembre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ora Inizio</label>
                        <input type="time" name="ora_inizio" class="form-control" value="{{ old('ora_inizio', $evento->ora_inizio?->format('H:i')) }}">
                    </div>
                    <div class="form-group">
                        <label>Durata (minuti)</label>
                        <input type="number" name="durata_minuti" class="form-control" value="{{ old('durata_minuti', $evento->durata_minuti ?? 60) }}" min="1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-folder mr-2"></i>Gruppi</h3></div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <select name="gruppi[]" class="form-control" multiple size="8">
                            @foreach($gruppi as $g)
                                <option value="{{ $g->id }}" {{ $evento->gruppi->contains($g->id) ? 'selected' : '' }}>
                                    {{ $g->full_path }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Tieni premuto Ctrl per selezionare più gruppi</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-user mr-2"></i>Responsabili</h3></div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <select name="responsabili[]" class="form-control" multiple size="8">
                            @foreach($individui as $i)
                                <option value="{{ $i->id }}" {{ $evento->responsabili->contains($i->id) ? 'selected' : '' }}>
                                    {{ $i->cognome }} {{ $i->nome }}
                                    @if($i->telefono_primario)
                                        ({{ $i->telefono_primario }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Tieni premuto Ctrl per selezionare più responsabili</small>
                    </div>
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

    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Salva Modifiche
        </button>
        <a href="{{ url('/eventi/' . $evento->id) }}" class="btn btn-secondary">
            <i class="fas fa-times mr-1"></i> Annulla
        </a>
    </div>
</form>
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
function toggleRicorrenza() {
    var tipo = document.getElementById('tipo_recorrenza').value;
    document.getElementById('data_specifica_field').style.display = tipo === 'singolo' ? 'block' : 'none';
    document.getElementById('settimanale_field').style.display = tipo === 'settimanale' ? 'block' : 'none';
    document.getElementById('mensile_field').style.display = tipo === 'mensile' ? 'block' : 'none';
    document.getElementById('annuale_field').style.display = tipo === 'annuale' ? 'block' : 'none';
}

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