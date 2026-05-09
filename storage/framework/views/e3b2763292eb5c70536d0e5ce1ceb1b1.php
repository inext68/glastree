<?php $__env->startSection('title', $mailingList->nome); ?>
<?php $__env->startSection('page_title', $mailingList->nome); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Dettagli Lista</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nome</dt>
                    <dd class="col-sm-8"><?php echo e($mailingList->nome); ?></dd>

                    <dt class="col-sm-4">Descrizione</dt>
                    <dd class="col-sm-8"><?php echo e($mailingList->descrizione ?: '-'); ?></dd>

                    <dt class="col-sm-4">Stato</dt>
                    <dd class="col-sm-8">
                        <?php if($mailingList->attiva): ?>
                        <span class="badge badge-success">Attiva</span>
                        <?php else: ?>
                        <span class="badge badge-secondary">Disattiva</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-4">Creata</dt>
                    <dd class="col-sm-8"><?php echo e($mailingList->created_at ? $mailingList->created_at->format('d/m/Y H:i') : '-'); ?></dd>

                    <dt class="col-sm-4">Creato da</dt>
                    <dd class="col-sm-8"><?php echo e($mailingList->user?->name ?: '-'); ?></dd>

                    <dt class="col-sm-4">Contatti</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-info"><?php echo e($mailingList->contatti->count()); ?></span>
                    </dd>
                </dl>

                <a href="<?php echo e(route('mailing-liste.edit', $mailingList->id)); ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Modifica
                </a>
                <form action="<?php echo e(route('mailing-liste.destroy', $mailingList->id)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Eliminare questa lista?')">
                        <i class="fas fa-trash mr-1"></i> Elimina
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Contatti (<?php echo e($mailingList->contatti->count()); ?>)</h3>
            </div>
            <div class="card-body p-0">
                <?php if($mailingList->contatti->count() > 0): ?>
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Codice</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $mailingList->contatti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($contact->individuo): ?>
                        <tr>
                            <td><span class="badge badge-secondary"><?php echo e($contact->individuo->codice_id); ?></span></td>
                            <td><?php echo e($contact->individuo->cognome); ?> <?php echo e($contact->individuo->nome); ?></td>
                            <td>
                                <?php
                                $email = $contact->individuo->contatti->where('tipo', 'email')->first()?->valore;
                                ?>
                                <?php echo e($email ?: '-'); ?>

                            </td>
                            <td>
                                <?php if($contact->opt_in): ?>
                                <span class="badge badge-success">Iscritto</span>
                                <?php else: ?>
                                <span class="badge badge-danger">Disiscritto</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <p>Nessun contatto in questa lista</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/mailing-liste/show.blade.php ENDPATH**/ ?>