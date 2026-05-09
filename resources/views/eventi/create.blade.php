@extends('layouts.adminlte')
@section('title', 'Nuovo Evento')
@section('page_title', 'Nuovo Evento')

@section('content')
<form action="{{ url('/eventi') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar mr-2"></i>Dati Evento</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nome Evento *</label>
                        <input type="text" name="nome_evento" class="form-control @error('nome_evento') is-invalid @enderror" value="{{ old('nome_evento') }}" required>
                        @error('nome_evento')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Descrizione Breve</label>
                        <input type="text" name="descrizione_evento" class="form-control" value="{{ old('descrizione_evento') }}" placeholder="Breve descrizione">
                    </div>
                    <div class="form-group">
                        <label>Descrizione Completa</label>
                        <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_incontro_gruppo" value="1" {{ old('is_incontro_gruppo') ? 'checked' : '' }}>
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
                            <option value="singolo" {{ old('tipo_recorrenza', 'singolo') === 'singolo' ? 'selected' : '' }}>Singolo</option>
                            <option value="settimanale" {{ old('tipo_recorrenza') === 'settimanale' ? 'selected' : '' }}>Settimanale</option>
                            <option value="mensile" {{ old('tipo_recorrenza') === 'mensile' ? 'selected' : '' }}>Mensile</option>
                            <option value="annuale" {{ old('tipo_recorrenza') === 'annuale' ? 'selected' : '' }}>Annuale</option>
                            <option value="altro" {{ old('tipo_recorrenza') === 'altro' ? 'selected' : '' }}>Altro</option>
                        </select>
                    </div>

                    <div id="data_specifica_field" style="{{ old('tipo_recorrenza') === 'singolo' || old('tipo_recorrenza') === null ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Data Evento</label>
                            <input type="date" name="data_specifica" class="form-control" value="{{ old('data_specifica') }}">
                        </div>
                    </div>

                    <div id="settimanale_field" style="{{ old('tipo_recorrenza') === 'settimanale' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Giorno della Settimana</label>
                            <select name="giorno_settimana" class="form-control">
                                <option value="">Seleziona...</option>
                                <option value="0" {{ old('giorno_settimana') === '0' ? 'selected' : '' }}>Domenica</option>
                                <option value="1" {{ old('giorno_settimana') == '1' ? 'selected' : '' }}>Lunedì</option>
                                <option value="2" {{ old('giorno_settimana') == '2' ? 'selected' : '' }}>Martedì</option>
                                <option value="3" {{ old('giorno_settimana') == '3' ? 'selected' : '' }}>Mercoledì</option>
                                <option value="4" {{ old('giorno_settimana') == '4' ? 'selected' : '' }}>Giovedì</option>
                                <option value="5" {{ old('giorno_settimana') == '5' ? 'selected' : '' }}>Venerdì</option>
                                <option value="6" {{ old('giorno_settimana') == '6' ? 'selected' : '' }}>Sabato</option>
                            </select>
                        </div>
                    </div>

                    <div id="mensile_field" style="{{ old('tipo_recorrenza') === 'mensile' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Occorrenza nel mese</label>
                            <select name="occorrenza_mese" class="form-control">
                                <option value="">Seleziona...</option>
                                <option value="1,1" {{ old('occorrenza_mese') === '1,1' ? 'selected' : '' }}>1° Lunedì</option>
                                <option value="1,2" {{ old('occorrenza_mese') === '1,2' ? 'selected' : '' }}>1° Martedì</option>
                                <option value="1,3" {{ old('occorrenza_mese') === '1,3' ? 'selected' : '' }}>1° Mercoledì</option>
                                <option value="1,4" {{ old('occorrenza_mese') === '1,4' ? 'selected' : '' }}>1° Giovedì</option>
                                <option value="1,5" {{ old('occorrenza_mese') === '1,5' ? 'selected' : '' }}>1° Venerdì</option>
                                <option value="1,6" {{ old('occorrenza_mese') === '1,6' ? 'selected' : '' }}>1° Sabato</option>
                                <option value="1,0" {{ old('occorrenza_mese') === '1,0' ? 'selected' : '' }}>1° Domenica</option>
                                <option value="2,1" {{ old('occorrenza_mese') === '2,1' ? 'selected' : '' }}>2° Lunedì</option>
                                <option value="2,2" {{ old('occorrenza_mese') === '2,2' ? 'selected' : '' }}>2° Martedì</option>
                                <option value="2,3" {{ old('occorrenza_mese') === '2,3' ? 'selected' : '' }}>2° Mercoledì</option>
                                <option value="2,4" {{ old('occorrenza_mese') === '2,4' ? 'selected' : '' }}>2° Giovedì</option>
                                <option value="2,5" {{ old('occorrenza_mese') === '2,5' ? 'selected' : '' }}>2° Venerdì</option>
                                <option value="2,6" {{ old('occorrenza_mese') === '2,6' ? 'selected' : '' }}>2° Sabato</option>
                                <option value="2,0" {{ old('occorrenza_mese') === '2,0' ? 'selected' : '' }}>2° Domenica</option>
                                <option value="3,1" {{ old('occorrenza_mese') === '3,1' ? 'selected' : '' }}>3° Lunedì</option>
                                <option value="3,2" {{ old('occorrenza_mese') === '3,2' ? 'selected' : '' }}>3° Martedì</option>
                                <option value="3,3" {{ old('occorrenza_mese') === '3,3' ? 'selected' : '' }}>3° Mercoledì</option>
                                <option value="3,4" {{ old('occorrenza_mese') === '3,4' ? 'selected' : '' }}>3° Giovedì</option>
                                <option value="3,5" {{ old('occorrenza_mese') === '3,5' ? 'selected' : '' }}>3° Venerdì</option>
                                <option value="3,6" {{ old('occorrenza_mese') === '3,6' ? 'selected' : '' }}>3° Sabato</option>
                                <option value="3,0" {{ old('occorrenza_mese') === '3,0' ? 'selected' : '' }}>3° Domenica</option>
                                <option value="4,1" {{ old('occorrenza_mese') === '4,1' ? 'selected' : '' }}>4° Lunedì</option>
                                <option value="4,2" {{ old('occorrenza_mese') === '4,2' ? 'selected' : '' }}>4° Martedì</option>
                                <option value="4,3" {{ old('occorrenza_mese') === '4,3' ? 'selected' : '' }}>4° Mercoledì</option>
                                <option value="4,4" {{ old('occorrenza_mese') === '4,4' ? 'selected' : '' }}>4° Giovedì</option>
                                <option value="4,5" {{ old('occorrenza_mese') === '4,5' ? 'selected' : '' }}>4° Venerdì</option>
                                <option value="4,6" {{ old('occorrenza_mese') === '4,6' ? 'selected' : '' }}>4° Sabato</option>
                                <option value="4,0" {{ old('occorrenza_mese') === '4,0' ? 'selected' : '' }}>4° Domenica</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mesi (multipli)</label>
                            <select name="mesi_recorrenza[]" class="form-control" multiple size="4">
                                <option value="1" {{ is_array(old('mesi_recorrenza')) && in_array('1', old('mesi_recorrenza')) ? 'selected' : '' }}>Gennaio</option>
                                <option value="2" {{ is_array(old('mesi_recorrenza')) && in_array('2', old('mesi_recorrenza')) ? 'selected' : '' }}>Febbraio</option>
                                <option value="3" {{ is_array(old('mesi_recorrenza')) && in_array('3', old('mesi_recorrenza')) ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ is_array(old('mesi_recorrenza')) && in_array('4', old('mesi_recorrenza')) ? 'selected' : '' }}>Aprile</option>
                                <option value="5" {{ is_array(old('mesi_recorrenza')) && in_array('5', old('mesi_recorrenza')) ? 'selected' : '' }}>Maggio</option>
                                <option value="6" {{ is_array(old('mesi_recorrenza')) && in_array('6', old('mesi_recorrenza')) ? 'selected' : '' }}>Giugno</option>
                                <option value="7" {{ is_array(old('mesi_recorrenza')) && in_array('7', old('mesi_recorrenza')) ? 'selected' : '' }}>Luglio</option>
                                <option value="8" {{ is_array(old('mesi_recorrenza')) && in_array('8', old('mesi_recorrenza')) ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ is_array(old('mesi_recorrenza')) && in_array('9', old('mesi_recorrenza')) ? 'selected' : '' }}>Settembre</option>
                                <option value="10" {{ is_array(old('mesi_recorrenza')) && in_array('10', old('mesi_recorrenza')) ? 'selected' : '' }}>Ottobre</option>
                                <option value="11" {{ is_array(old('mesi_recorrenza')) && in_array('11', old('mesi_recorrenza')) ? 'selected' : '' }}>Novembre</option>
                                <option value="12" {{ is_array(old('mesi_recorrenza')) && in_array('12', old('mesi_recorrenza')) ? 'selected' : '' }}>Dicembre</option>
                            </select>
                            <small class="form-text text-muted">Ctrl+clic per selezionare più mesi</small>
                        </div>
                    </div>

                    <div id="annuale_field" style="{{ old('tipo_recorrenza') === 'annuale' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Mese</label>
                            <select name="mese_annuale" class="form-control">
                                <option value="">Seleziona mese...</option>
                                <option value="1" {{ old('mese_annuale') == '1' ? 'selected' : '' }}>Gennaio</option>
                                <option value="2" {{ old('mese_annuale') == '2' ? 'selected' : '' }}>Febbraio</option>
                                <option value="3" {{ old('mese_annuale') == '3' ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ old('mese_annuale') == '4' ? 'selected' : '' }}>Aprile</option>
                                <option value="5" {{ old('mese_annuale') == '5' ? 'selected' : '' }}>Maggio</option>
                                <option value="6" {{ old('mese_annuale') == '6' ? 'selected' : '' }}>Giugno</option>
                                <option value="7" {{ old('mese_annuale') == '7' ? 'selected' : '' }}>Luglio</option>
                                <option value="8" {{ old('mese_annuale') == '8' ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ old('mese_annuale') == '9' ? 'selected' : '' }}>Settembre</option>
                                <option value="10" {{ old('mese_annuale') == '10' ? 'selected' : '' }}>Ottobre</option>
                                <option value="11" {{ old('mese_annuale') == '11' ? 'selected' : '' }}>Novembre</option>
                                <option value="12" {{ old('mese_annuale') == '12' ? 'selected' : '' }}>Dicembre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ora Inizio</label>
                        <input type="time" name="ora_inizio" class="form-control" value="{{ old('ora_inizio') }}">
                    </div>
                    <div class="form-group">
                        <label>Durata (minuti)</label>
                        <input type="number" name="durata_minuti" class="form-control" value="{{ old('durata_minuti', 60) }}" min="1">
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
                                <option value="{{ $g->id }}" {{ (old('gruppi') && in_array($g->id, old('gruppi'))) || ($selectedGruppo && $selectedGruppo->id == $g->id) ? 'selected' : '' }}>
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
                                <option value="{{ $i->id }}" {{ old('responsabili') && in_array($i->id, old('responsabili')) ? 'selected' : '' }}>
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
        <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Note</h3></div>
        <div class="card-body">
            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Salva
        </button>
        <a href="{{ url('/eventi') }}" class="btn btn-secondary">
            <i class="fas fa-times mr-1"></i> Annulla
        </a>
    </div>
</form>
@endsection

@section('scripts')
<script>
function toggleRicorrenza() {
    var tipo = document.getElementById('tipo_recorrenza').value;
    document.getElementById('data_specifica_field').style.display = tipo === 'singolo' ? 'block' : 'none';
    document.getElementById('settimanale_field').style.display = tipo === 'settimanale' ? 'block' : 'none';
    document.getElementById('mensile_field').style.display = tipo === 'mensile' ? 'block' : 'none';
    document.getElementById('annuale_field').style.display = tipo === 'annuale' ? 'block' : 'none';
}
</script>
@endsection