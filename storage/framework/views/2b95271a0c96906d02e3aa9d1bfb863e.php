<?php
$children = $gruppo->children()->with(['diocesi', 'responsabile', 'individui'])->orderBy('nome')->get();
$hasChildren = $children->count() > 0;
?>

<div class="tree-item" style="margin-left: <?php echo e($gruppo->depth ?? 0); ?>px;">
    <div class="tree-label">
        <?php if($hasChildren): ?>
            <span class="tree-toggle" onclick="toggleItem(<?php echo e($gruppo->id); ?>)">
                <i id="icon-<?php echo e($gruppo->id); ?>" class="fas fa-chevron-right text-muted"></i>
            </span>
        <?php else: ?>
            <span style="width: 22px; display: inline-block;"></span>
        <?php endif; ?>
        <i class="fas fa-<?php echo e($hasChildren ? 'folder' : 'folder-open'); ?> text-<?php echo e($gruppo->depth == 0 ? 'warning' : 'secondary'); ?> mr-1"></i>
        <a href="<?php echo e(route('gruppi.show', $gruppo->id)); ?>" class="<?php echo e(($gruppo->depth ?? 0) == 0 ? 'font-weight-bold' : ''); ?>">
            <?php echo e($gruppo->nome); ?>

        </a>
        <span class="badge badge-<?php echo e(($gruppo->depth ?? 0) == 0 ? 'primary' : 'secondary'); ?> badge-level"><?php echo e($gruppo->depth ?? 0); ?></span>
        <small class="text-muted ml-2">
            <?php echo e($gruppo->diocesi?->nome ?? ''); ?>

            <?php if($gruppo->responsabile): ?>
                · <i class="fas fa-user text-info"></i> <?php echo e($gruppo->responsabile->cognome); ?>

            <?php endif; ?>
        </small>
        <span class="ml-auto">
            <?php if($gruppo->individui_count > 0): ?>
                <span class="badge badge-info"><?php echo e($gruppo->individui_count); ?> membri</span>
            <?php endif; ?>
            <?php if($hasChildren): ?>
                <span class="badge badge-warning"><?php echo e($children->count()); ?> figli</span>
            <?php endif; ?>
        </span>
        <span class="ml-2">
            <a href="<?php echo e(route('gruppi.show', $gruppo->id)); ?>" class="btn btn-xs btn-info" title="Visualizza">
                <i class="fas fa-eye"></i>
            </a>
            <a href="<?php echo e(url('/gruppi/' . $gruppo->id . '/edit')); ?>" class="btn btn-xs btn-warning" title="Modifica">
                <i class="fas fa-edit"></i>
            </a>
            <form action="<?php echo e(url('/gruppi/' . $gruppo->id)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Confermi l\'eliminazione? Verranno eliminati anche tutti i sottogruppi.')" title="Elimina">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </span>
    </div>
</div>

<?php if($hasChildren): ?>
    <div id="group-<?php echo e($gruppo->id); ?>" class="tree-children" style="display: none;">
        <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('gruppi.partials.tree-item', ['gruppo' => $child], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?><?php /**PATH /var/www/html/glastree/resources/views/gruppi/partials/tree-item.blade.php ENDPATH**/ ?>