@extends('layouts.adminlte')
@section('title', 'Eventi')
@section('page_title', 'Eventi')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tutti gli Eventi</h3>
        <a href="{{ url('/eventi/create') }}" class="btn btn-xs btn-success float-right">
            <i class="fas fa-plus mr-1"></i> Nuovo Evento
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <select name="gruppo_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Tutti i gruppi</option>
                        @foreach($gruppi as $g)
                            <option value="{{ $g->id }}" {{ request('gruppo_id') == $g->id ? 'selected' : '' }}>{{ $g->full_path }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipo" class="form-control" onchange="this.form.submit()">
                        <option value="">Tutte le ricorrenze</option>
                        <option value="singolo" {{ request('tipo') == 'singolo' ? 'selected' : '' }}>Singolo</option>
                        <option value="settimanale" {{ request('tipo') == 'settimanale' ? 'selected' : '' }}>Settimanale</option>
                        <option value="mensile" {{ request('tipo') == 'mensile' ? 'selected' : '' }}>Mensile</option>
                        <option value="annuale" {{ request('tipo') == 'annuale' ? 'selected' : '' }}>Annuale</option>
                    </select>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Nome Evento</th>
                    <th>Descrizione</th>
                    <th>Tipo</th>
                    <th>Gruppi</th>
                    <th>Responsabili</th>
                    <th style="width: 80px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventi as $evento)
                <tr>
                    <td>
                        <a href="{{ url('/eventi/' . $evento->id) }}">
                            <i class="fas fa-calendar mr-1 text-primary"></i>
                            {{ $evento->nome_evento }}
                        </a>
                        @if($evento->is_incontro_gruppo)
                            <span class="badge badge-success ml-1">Incontro Gruppo</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($evento->descrizione_evento, 50) ?: '-' }}</td>
                    <td>
                        @if($evento->tipo_recorrenza && $evento->tipo_recorrenza !== 'singolo')
                            <span class="badge badge-info">{{ $evento->periodicita_label }}</span>
                        @else
                            <span class="badge badge-secondary">Singolo</span>
                        @endif
                    </td>
                    <td>
                        @forelse($evento->gruppi as $gruppo)
                            <a href="{{ url('/gruppi/' . $gruppo->id) }}">
                                <span class="badge badge-warning">{{ $gruppo->nome }}</span>
                            </a>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </td>
                    <td>
                        @forelse($evento->responsabili->take(2) as $resp)
                            <small>{{ $resp->nome_completo }}</small><br>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                        @if($evento->responsabili->count() > 2)
                            <small class="text-muted">+{{ $evento->responsabili->count() - 2 }} altri</small>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('/eventi/' . $evento->id) }}" class="btn btn-xs btn-info" title="Visualizza">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ url('/eventi/' . $evento->id . '/edit') }}" class="btn btn-xs btn-warning" title="Modifica">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-calendar fa-2x mb-2"></i>
                        <p class="mb-0">Nessun evento trovato</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $eventi->withQueryString()->links() }}
    </div>
</div>
@endsection