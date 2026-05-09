<?php $__env->startSection('title', 'Nuovo Messaggio'); ?>
<?php $__env->startSection('page_title', 'Nuovo Messaggio Email'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-9">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Componi Messaggio</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('mailing.invia')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

<?php if($individui->count() > 0): ?>
                    <div class="form-group" id="destinatari-form-group">
                        <label>Destinatari selezionati</label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="destinatari-tbody">
                                    <?php $__currentLoopData = $individui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ind): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr id="dest-<?php echo e($ind->id); ?>">
                                        <td><span class="badge badge-secondary"><?php echo e($ind->codice_id); ?></span></td>
                                        <td><strong><?php echo e($ind->cognito); ?></strong> <?php echo e($ind->nome); ?></td>
                                        <td><?php echo e($ind->contatti->where('tipo', 'email')->first()?->valore ?: 'Nessuna email'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeDestinatario(<?php echo e($ind->id); ?>)" title="Rimuovi">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="destinatari_ids" id="destinatari_ids" value="<?php echo e($individui->pluck('id')->join(',')); ?>">
                        <small class="text-muted" id="destinatari-count"><?php echo e($individui->count()); ?> destinatari</small>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label for="lista_id">Lista Destinatari</label>
                        <select name="lista_id" id="lista_id" class="form-control">
                            <option value="">Seleziona lista...</option>
                            <?php $__empty_1 = true; $__currentLoopData = $liste; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lista): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($lista->id); ?>"><?php echo e($lista->nome); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="">Nessuna lista disponibile</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="oggetto">Oggetto *</label>
                        <input type="text" name="oggetto" id="oggetto" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="corpo">Messaggio *</label>
                        <textarea name="corpo" id="corpo" class="form-control" rows="10" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Allegati</label>
                        <div class="border rounded p-3">
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#documentiModal">
                                    <i class="fas fa-folder-open mr-1"></i> Seleziona da Documenti
                                </button>
                                <label class="btn btn-sm btn-success mb-0">
                                    <i class="fas fa-upload mr-1"></i> Carica nuovo file
                                    <input type="file" name="allegato" id="allegato" class="d-none" onchange="handleFileUpload(this)">
                                </label>
                            </div>
                            <div id="allegati-list">
                                <?php if(!empty($documentiSelezionati)): ?>
                                <?php $__currentLoopData = $documentiSelezionati; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex align-items-center justify-content-between bg-light p-2 mb-2 rounded" id="allegato-<?php echo e($doc->id); ?>">
                                    <span><i class="fas fa-file mr-2"></i><?php echo e($doc->nome_file); ?></span>
                                    <button type="button" class="btn btn-xs btn-danger" onclick="removeAllegato(<?php echo e($doc->id); ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="documenti_selezionati" id="documenti_selezionati" value="">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Invia
                    </button>
                    <a href="<?php echo e(route('individui.index')); ?>" class="btn btn-secondary">
                        Annulla
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-folder-open mr-2"></i>Seleziona Documenti</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if($documenti->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Nome File</th>
                                <th>Tipologia</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $documenti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input doc-checkbox" id="doc-<?php echo e($doc->id); ?>" value="<?php echo e($doc->id); ?>" data-nome="<?php echo e($doc->nome_file); ?>">
                                        <label class="custom-control-label" for="doc-<?php echo e($doc->id); ?>"></label>
                                    </div>
                                </td>
                                <td><?php echo e($doc->nome_file); ?></td>
                                <td><span class="badge badge-info"><?php echo e($doc->tipologia ?: $doc->tipo); ?></span></td>
                                <td><?php echo e($doc->created_at->format('d/m/Y')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Nessun documento disponibile.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="aggiungiDocumenti()">
                    <i class="fas fa-plus mr-1"></i> Aggiungi
                </button>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
let allegatiSelezionati = [];

function removeDestinatario(id) {
    const row = document.getElementById('dest-' + id);
    if (!row) return;

    row.remove();

    const input = document.getElementById('destinatari_ids');
    if (input) {
        let ids = input.value.split(',').filter(function(i) {
            return i && parseInt(i) !== id;
        });
        input.value = ids.join(',');

        const countEl = document.getElementById('destinatari-count');
        if (countEl) {
            countEl.textContent = ids.length + ' destinatari';
        }

        if (ids.length === 0) {
            document.getElementById('destinatari-form-group').style.display = 'none';
            document.getElementById('lista-form-group').style.display = 'block';
        }
    }
}

function aggiungiDocumenti() {
    const checkboxes = document.querySelectorAll('.doc-checkbox:checked');
    checkboxes.forEach(function(cb) {
        const id = parseInt(cb.value);
        const nome = cb.dataset.nome;

        if (!allegatiSelezionati.includes(id)) {
            allegatiSelezionati.push(id);

            const container = document.getElementById('allegati-list');
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center justify-content-between bg-light p-2 mb-2 rounded';
            div.id = 'allegato-' + id;
            div.innerHTML = '<span><i class="fas fa-file mr-2"></i>' + nome + '</span>' +
                '<button type="button" class="btn btn-xs btn-danger" onclick="removeAllegato(' + id + ')">' +
                '<i class="fas fa-times"></i></button>';
            container.appendChild(div);
        }

        cb.checked = false;
    });

    document.getElementById('documenti_esistenti').value = allegatiSelezionati.join(',');
    $('#documentiModal').modal('hide');
}

function removeAllegato(id) {
    allegatiSelezionati = allegatiSelezionati.filter(function(a) { return a !== id; });
    const el = document.getElementById('allegato-' + id);
    if (el) el.remove();
    document.getElementById('documenti_selezionati').value = allegatiSelezionati.join(',');
}

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const container = document.getElementById('allegati-list');
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center justify-content-between bg-light p-2 mb-2 rounded';
        div.id = 'allegato-upload';
        div.innerHTML = '<span><i class="fas fa-file mr-2"></i>' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</span>' +
            '<button type="button" class="btn btn-xs btn-danger" onclick="this.parentElement.remove(); input.value=\'\';">' +
            '<i class="fas fa-times"></i></button>';
        container.appendChild(div);
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/mailing/nuovo.blade.php ENDPATH**/ ?>