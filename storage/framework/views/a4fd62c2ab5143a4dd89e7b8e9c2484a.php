<?php $__env->startSection('title', 'Mailing Lists'); ?>
<?php $__env->startSection('page_title', 'Mailing Lists'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Elenco Mailing Lists</h3>
        <div class="card-tools">
            <a href="<?php echo e(route('mailing-liste.create')); ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Nuova Lista
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if($mailingLists->count() > 0): ?>
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th style="width: 100px;">Contatti</th>
                    <th style="width: 100px;">Stato</th>
                    <th style="width: 130px;">Creata il</th>
                    <th style="width: 100px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $mailingLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lista): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <a href="<?php echo e(route('mailing-liste.show', $lista->id)); ?>"><?php echo e($lista->nome); ?></a>
                    </td>
                    <td><?php echo e($lista->descrizione ?: '-'); ?></td>
                    <td>
                        <span class="badge badge-info"><?php echo e($lista->contatti->count()); ?></span>
                    </td>
                    <td>
                        <?php if($lista->attiva): ?>
                        <span class="badge badge-success">Attiva</span>
                        <?php else: ?>
                        <span class="badge badge-secondary">Disattiva</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($lista->created_at->format('d/m/Y')); ?></td>
                    <td>
                        <a href="<?php echo e(route('mailing-liste.show', $lista->id)); ?>" class="btn btn-xs btn-info" title="Visualizza">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?php echo e(route('mailing-liste.edit', $lista->id)); ?>" class="btn btn-xs btn-warning" title="Modifica">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('mailing-liste.destroy', $lista->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare questa lista?')" title="Elimina">
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
            <i class="fas fa-list fa-3x mb-3"></i>
            <p>Nessuna mailing list</p>
            <a href="<?php echo e(route('mailing-liste.create')); ?>" class="btn btn-success">Crea la prima lista</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/mailing-liste/index.blade.php ENDPATH**/ ?>