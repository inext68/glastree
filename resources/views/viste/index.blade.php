@extends('layouts.adminlte')
@section('title', 'Viste Report')
@section('page_title', 'Viste e Report Salvati')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-table mr-2"></i>Le Tue Viste</h3>
    </div>
    <div class="card-body p-0">
        @if($viste->count() > 0)
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Nome</th>
                    <th style="width: 120px;">Tipo</th>
                    <th style="width: 100px;">Colonne</th>
                    <th style="width: 150px;">Creata il</th>
                    <th style="width: 120px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($viste as $vista)
                <tr>
                    <td>
                        @if($vista->is_default)
                        <i class="fas fa-star text-warning mr-1" title="Predefinita"></i>
                        @else
                        <i class="fas fa-file-alt text-primary mr-1"></i>
                        @endif
                        {{ $vista->nome }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $vista->tipo === 'individui' ? 'info' : ($vista->tipo === 'gruppi' ? 'warning' : ($vista->tipo === 'documenti' ? 'success' : 'primary')) }}">
                            {{ ucfirst($vista->tipo) }}
                        </span>
                    </td>
                    <td>
                        @if($vista->colonne_visibili)
                        <span class="badge badge-secondary">{{ count($vista->colonne_visibili) }}</span>
                        @else
                        <span class="text-muted">Tutte</span>
                        @endif
                    </td>
                    <td>{{ $vista->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ url('/' . $vista->tipo . '?vista_id=' . $vista->id) }}" class="btn btn-xs btn-primary" title="Applica">
                            <i class="fas fa-play"></i>
                        </a>
                        @if(!$vista->is_default)
                        <form action="{{ url('/viste/' . $vista->id . '/default') }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-xs btn-warning" title="Imposta predefinita">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                        @else
                        <span class="btn btn-xs btn-secondary" title="Predefinita">
                            <i class="fas fa-check"></i>
                        </span>
                        @endif
                        <form action="{{ url('/viste/' . $vista->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare questa vista?')" title="Elimina">
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
            <i class="fas fa-table fa-2x mb-2"></i>
            <p class="mb-0">Nessuna vista salvata.<br>Configura una tabella e salva la vista.</p>
        </div>
        @endif
    </div>
</div>

<div class="mt-3 text-muted small">
    <i class="fas fa-info-circle mr-1"></i>
    Per salvare una vista, configura le colonne, i filtri e l'ordinamento in una delle pagine elenco (Individui, Gruppi, Documenti, Eventi) e clicca su "Salva Vista".
</div>
@endsection