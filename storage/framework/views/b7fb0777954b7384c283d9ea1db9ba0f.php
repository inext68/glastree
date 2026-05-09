<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo e($stats['individui']); ?></h3>
                <p>Individui</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="<?php echo e(route('individui.index')); ?>" class="small-box-footer">Visualizza <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo e($stats['gruppi']); ?></h3>
                <p>Gruppi</p>
            </div>
            <div class="icon">
                <i class="fas fa-folder"></i>
            </div>
            <a href="<?php echo e(route('gruppi.index')); ?>" class="small-box-footer">Visualizza <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo e($stats['notifiche']); ?></h3>
                <p>Notifiche</p>
            </div>
            <div class="icon">
                <i class="fas fa-bell"></i>
            </div>
            <a href="#" class="small-box-footer">Visualizza <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell"></i> Ultime Notifiche</h3>
            </div>
            <div class="card-body">
                <?php if($notifiche->count() > 0): ?>
                <ul class="todo-list" data-widget="todo-list">
                    <?php $__currentLoopData = $notifiche; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notifica): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span class="text"><?php echo e($notifica->titolo); ?></span>
                        <small class="badge badge-info"><i class="far fa-clock"></i> <?php echo e($notifica->created_at->diffForHumans()); ?></small>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php else: ?>
                <p class="text-muted">Nessuna notifica</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Benvenuto</h3>
            </div>
            <div class="card-body">
                <p>Benvenuto in <strong>Glastree</strong>, il sistema di gestione per associazioni.</p>
                <p>Dal menu laterale puoi navigare tra le sezioni:</p>
                <ul>
                    <li><strong>Individui</strong>: Gestione delle persone registrate</li>
                    <li><strong>Gruppi</strong>: Gestione della struttura organizzativa</li>
                    <li><strong>Documenti</strong>: Archivio documentale</li>
                    <li><strong>Eventi</strong>: Calendario eventi</li>
                    <li><strong>Mailing</strong>: Comunicazioni email</li>
                </ul>
                <?php if(Auth::user()->is_admin): ?>
                <div class="alert alert-success mt-3">
                    <i class="fas fa-crown"></i> <strong>Amministratore</strong>: Hai accesso completo al sistema.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/home/dashboard.blade.php ENDPATH**/ ?>