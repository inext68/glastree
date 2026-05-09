<?php $__env->startSection('title', 'Gruppi'); ?>
<?php $__env->startSection('page_title', 'Elenco Gruppi'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-sitemap mr-2"></i>Albero Gruppi</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-2" onclick="toggleAllGroups()">
                <i class="fas fa-expand-alt"></i> Espandi/Tutti
            </button>
            <a href="<?php echo e(route('gruppi.create')); ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nuovo Gruppo
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="tree-view" style="padding: 15px;">
            <?php $__empty_1 = true; $__currentLoopData = $rootGruppi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gruppo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php echo $__env->make('gruppi.partials.tree-item', ['gruppo' => $gruppo, 'isRoot' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                    <p>Nessun gruppo presente. <a href="<?php echo e(route('gruppi.create')); ?>">Crea il primo gruppo</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function toggleItem(id) {
    const el = document.getElementById('group-' + id);
    const icon = document.getElementById('icon-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        el.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

function toggleAllGroups() {
    const collapsed = document.querySelectorAll('.tree-children[style*="display: none"]').length > 0;
    document.querySelectorAll('.tree-children').forEach(el => {
        el.style.display = collapsed ? 'block' : 'none';
    });
    document.querySelectorAll('.tree-toggle i').forEach(icon => {
        if (collapsed) {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-down');
        } else {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-right');
        }
    });
}
</script>
<style>
.tree-item { margin-bottom: 5px; }
.tree-children { 
    margin-left: 25px; 
    border-left: 1px dashed #dee2e6; 
    padding-left: 10px; 
}
.tree-toggle {
    cursor: pointer;
    padding: 3px 8px;
    border-radius: 4px;
}
.tree-toggle:hover {
    background-color: #f8f9fa;
}
.tree-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.badge-level {
    font-size: 0.7em;
    padding: 2px 5px;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/gruppi/index.blade.php ENDPATH**/ ?>