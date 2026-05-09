<?php $__env->startSection('title', 'Dettaglio Gruppo'); ?>
<?php $__env->startSection('page_title', 'Dettaglio Gruppo'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('gruppi.index')); ?>">Gruppi</a></li>
<li class="breadcrumb-item active"><?php echo e($gruppo->nome); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-folder text-warning mr-2"></i>
                    <?php echo e($gruppo->nome); ?>

                </h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td style="width: 130px;"><strong>Path:</strong></td>
                        <td class="text-muted small"><?php echo e($gruppo->full_path); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gruppo Padre:</strong></td>
                        <td>
                            <?php if($gruppo->parent): ?>
                                <a href="<?php echo e(route('gruppi.show', $gruppo->parent->id)); ?>"><?php echo e($gruppo->parent->nome); ?></a>
                            <?php else: ?>
                                <span class="badge badge-success">Gruppo radice</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Diocesi:</strong></td>
                        <td><?php echo e($gruppo->diocesi?->nome ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Responsabile:</strong></td>
                        <td>
                            <?php
                                $responsabile = $gruppo->individui->firstWhere('id', $gruppo->responsabile_id);
                            ?>
                            <?php if($responsabile): ?>
                                <a href="<?php echo e(route('individui.show', $responsabile->id)); ?>">
                                    <i class="fas fa-user text-info mr-1"></i>
                                    <?php echo e($responsabile->nome_completo); ?>

                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Luogo Incontro</h3></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td style="width: 100px;"><strong>Indirizzo:</strong></td>
                        <td><?php echo e($gruppo->indirizzo_incontro ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>CAP:</strong></td>
                        <td><?php echo e($gruppo->cap_incontro ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Città:</strong></td>
                        <td><?php echo e($gruppo->città_incontro ?: '-'); ?> <?php echo e($gruppo->sigla_provincia_incontro ? "({$gruppo->sigla_provincia_incontro})" : ''); ?></td>
                    </tr>
                </table>
                <?php if($gruppo->mappa_posizione): ?>
                    <a href="<?php echo e($gruppo->mappa_posizione); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Visualizza su mappa
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Statistiche</h3></div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-right">
                            <h2 class="mb-0 text-primary"><?php echo e($gruppo->individui->count()); ?></h2>
                            <small class="text-muted">Membri</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h2 class="mb-0 text-warning"><?php echo e($gruppo->children->count()); ?></h2>
                        <small class="text-muted">Sottogruppi</small>
                    </div>
</div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $incontroEvento = $gruppo->eventi->where('is_incontro_gruppo', true)->first();
    ?>
    <?php if($incontroEvento): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Giorno di Incontro
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 100px;"><strong>Evento:</strong></td>
                            <td><?php echo e($incontroEvento->nome_evento); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Quando:</strong></td>
                            <td>
                                <?php if($incontroEvento->tipo_recorrenza === 'settimanale'): ?>
                                    <?php echo e($incontroEvento->giorno_settimana_label); ?>

                                    <span class="badge badge-info ml-1">Settimanale</span>
                                <?php elseif($incontroEvento->tipo_recorrenza === 'mensile'): ?>
                                    <?php echo e($incontroEvento->occorrenza_mensile_label); ?>

                                    <?php if($incontroEvento->mesi_recorrenza): ?>
                                        <small class="text-muted">(<?php echo e($incontroEvento->mesi_recorrenza_label); ?>)</small>
                                    <?php endif; ?>
                                    <span class="badge badge-info ml-1">Mensile</span>
                                <?php elseif($incontroEvento->tipo_recorrenza === 'annuale'): ?>
                                    <?php echo e($incontroEvento->mese_annuale_label); ?>

                                    <span class="badge badge-info ml-1">Annuale</span>
                                <?php elseif($incontroEvento->tipo_recorrenza === 'altro'): ?>
                                    <?php echo e($incontroEvento->giorno_settimana_label); ?>

                                    <span class="badge badge-info ml-1">Altro</span>
                                <?php else: ?>
                                    <?php echo e($incontroEvento->data_specifica?->format('d/m/Y') ?: '-'); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if($incontroEvento->ora_inizio): ?>
                        <tr>
                            <td><strong>Ora:</strong></td>
                            <td>ore <?php echo e($incontroEvento->ora_inizio->format('H:i')); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <?php if($incontroEvento->responsabili->count() > 0): ?>
                    <hr>
                    <small class="text-muted"><strong>Responsabili:</strong></small>
                    <?php $__currentLoopData = $incontroEvento->responsabili; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mt-1">
                            <i class="fas fa-user text-info mr-1"></i>
                            <a href="<?php echo e(url('/individui/' . $resp->id)); ?>"><?php echo e($resp->cognome); ?> <?php echo e($resp->nome); ?></a>
                            <?php if($resp->telefono_primario): ?>
                                <br><small class="text-success ml-3"><?php echo e($resp->telefono_primario); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <div class="mt-2">
                        <a href="<?php echo e(url('/eventi/' . $incontroEvento->id)); ?>" class="btn btn-xs btn-outline-primary">
                            <i class="fas fa-eye mr-1"></i> Vedi evento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($gruppo->descrizione): ?>
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-align-left mr-2"></i>Descrizione</h3></div>
            <div class="card-body">
                <p class="mb-0"><?php echo nl2br(e($gruppo->descrizione)); ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>Membri (<?php echo e($gruppo->individui->count()); ?>)
        </h3>
    </div>
    <div class="card-body p-0">
        <?php if($gruppo->individui->count() > 0): ?>
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 100px;">Codice</th>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th style="width: 150px;">Ruolo</th>
                        <th style="width: 110px;">Data Adesione</th>
                        <th style="width: 80px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $gruppo->individui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $individuo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><span class="badge badge-secondary"><?php echo e($individuo->codice_id); ?></span></td>
                        <td>
                            <a href="<?php echo e(route('individui.show', $individuo->id)); ?>">
                                <i class="fas fa-user text-info mr-1"></i>
                                <?php echo e($individuo->cognome); ?> <?php echo e($individuo->nome); ?>

                            </a>
                        </td>
                        <td><?php echo e($individuo->email_primaria ?? '-'); ?></td>
                        <td><?php echo e($individuo->telefono_primario ?? '-'); ?></td>
                        <td>
                            <?php if($individuo->pivot->ruolo_nel_gruppo): ?>
                                <span class="badge badge-secondary"><?php echo e($individuo->pivot->ruolo_nel_gruppo); ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($individuo->pivot->data_adesione): ?>
                                <?php echo e(\Carbon\Carbon::parse($individuo->pivot->data_adesione)->format('d/m/Y')); ?>

                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('individui.show', $individuo->id)); ?>" class="btn btn-xs btn-info" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p class="mb-0">Nessun membro</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file mr-2"></i>Documenti (<?php echo e($gruppo->documenti->count()); ?>)
        </h3>
        <a href="<?php echo e(url('/gruppi/' . $gruppo->id . '/edit')); ?>" class="btn btn-xs btn-primary float-right">
            <i class="fas fa-upload mr-1"></i> Carica
        </a>
    </div>
    <div class="card-body p-0">
        <?php if($gruppo->documenti->count() > 0): ?>
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nome</th>
                        <th style="width: 120px;">Tipologia</th>
                        <th style="width: 80px;">Dimensione</th>
                        <th style="width: 130px;">Data Upload</th>
                        <th style="width: 100px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $gruppo->documenti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <i class="fas fa-file text-secondary mr-1"></i>
                            <?php echo e($documento->nome_file); ?>

                        </td>
                        <td><?php echo e(ucfirst(str_replace('_', ' ', $documento->tipologia))); ?></td>
                        <td><?php echo e(number_format($documento->dimensione / 1024, 1)); ?> KB</td>
                        <td><?php echo e($documento->created_at->format('d/m/Y')); ?></td>
                        <td>
                            <?php if($documento->file_path): ?>
                                <a href="<?php echo e(url('/documenti/' . $documento->id . '/download')); ?>" class="btn btn-xs btn-primary" title="Scarica">
                                    <i class="fas fa-download"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-file fa-2x mb-2"></i>
                <p class="mb-0">Nessun documento</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sitemap mr-2"></i>Sottogruppi (<?php echo e($gruppo->children->count()); ?>)
        </h3>
        <a href="<?php echo e(route('gruppi.create', ['parent_id' => $gruppo->id])); ?>" class="btn btn-xs btn-success float-right">
            <i class="fas fa-plus mr-1"></i> Aggiungi Sottogruppo
        </a>
    </div>
    <div class="card-body p-0">
        <?php if($gruppo->children->count() > 0): ?>
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nome</th>
                        <th>Diocesi</th>
                        <th>Responsabile</th>
                        <th style="text-align: center;">Membri</th>
                        <th style="width: 100px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $gruppo->children->sortBy('nome'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('gruppi.show', $child->id)); ?>">
                                <i class="fas fa-folder-open text-warning mr-1"></i>
                                <?php echo e($child->nome); ?>

                            </a>
                        </td>
                        <td><?php echo e($child->diocesi?->nome ?? '-'); ?></td>
                        <td><?php echo e($child->responsabile?->nome_completo ?? '-'); ?></td>
                        <td class="text-center">
                            <?php if($child->individui()->count() > 0): ?>
                                <span class="badge badge-info"><?php echo e($child->individui()->count()); ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('gruppi.show', $child->id)); ?>" class="btn btn-xs btn-info" title="Visualizza">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('gruppi.edit', $child->id)); ?>" class="btn btn-xs btn-warning" title="Modifica">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('gruppi.destroy', $child->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare <?php echo e($child->nome); ?>?')" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-folder-open fa-2x mb-2"></i>
                <p class="mb-0">Nessun sottogruppo</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo e(url('/gruppi/' . $gruppo->id . '/edit')); ?>" class="btn btn-warning">
        <i class="fas fa-edit mr-1"></i> Modifica
    </a>
    <form action="<?php echo e(url('/gruppi/' . $gruppo->id)); ?>" method="POST" class="d-inline">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Confermi l\'eliminazione di <?php echo e($gruppo->nome); ?>? Verranno eliminati anche tutti i sottogruppi.')">
            <i class="fas fa-trash mr-1"></i> Elimina
        </button>
    </form>
    <a href="<?php echo e(route('gruppi.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Torna all'elenco
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/gruppi/show.blade.php ENDPATH**/ ?>