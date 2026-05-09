<?php $__env->startSection('title', 'Modifica Mailing List'); ?>
<?php $__env->startSection('page_title', 'Modifica Mailing List'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Modifica Mailing List</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo e(url('/mailing-liste/' . $mailingList->id)); ?>" method="POST" id="ml-form">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="nome" class="form-control" value="<?php echo e($mailingList->nome); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="checkbox" class="custom-control-input" id="attiva" name="attiva" value="1" <?php echo e($mailingList->attiva ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="attiva">Lista attiva</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Descrizione</label>
                <textarea name="descrizione" class="form-control" rows="2"><?php echo e($mailingList->descrizione); ?></textarea>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Seleziona Gruppi</label>
                        <select name="gruppi[]" id="gruppi-select" class="form-control" multiple size="6">
                        </select>
                        <small class="text-muted">Seleziona con Ctrl/Cmd (ogni membro del gruppo sarà aggiunto)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Seleziona Individui</label>
                        <select name="individui[]" id="individui-select" class="form-control" multiple size="6">
                        </select>
                        <small class="text-muted">Seleziona con Ctrl/Cmd</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="caricaSelezionati()">
                    <i class="fas fa-search mr-1"></i> Carica contatti
                </button>
                <button type="button" class="btn btn-secondary" onclick="selezionaTutti()">
                    <i class="fas fa-check-square mr-1"></i> Seleziona tutti
                </button>
                <button type="button" class="btn btn-warning" onclick="deselezionaTutti()">
                    <i class="fas fa-square mr-1"></i> Deseleziona tutti
                </button>
            </div>

            <div class="form-group">
                <label>Contatti da includere (<span id="contatti-count"><?php echo e($mailingList->contatti->count()); ?></span>)</label>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="select-all" onchange="toggleSelectAll(this)">
                                        <label class="custom-control-label" for="select-all"></label>
                                    </div>
                                </th>
                                <th>Codice</th>
                                <th>Cognome</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Stato</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="contatti-tbody">
                            <?php $__currentLoopData = $mailingList->contatti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($contact->individuo): ?>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input contatto-checkbox" id="contatto-<?php echo e($contact->id); ?>" value="<?php echo e($contact->individuo->id); ?>" data-email="<?php echo e($contact->individuo->contatti->where('tipo', 'email')->first()?->valore); ?>" checked>
                                        <label class="custom-control-label" for="contatto-<?php echo e($contact->id); ?>"></label>
                                    </div>
                                </td>
                                <td><span class="badge badge-secondary"><?php echo e($contact->individuo->codice_id); ?></span></td>
                                <td><?php echo e($contact->individuo->cogname); ?></td>
                                <td><?php echo e($contact->individuo->nome); ?></td>
                                <td><?php echo e($contact->individuo->contatti->where('tipo', 'email')->first()?->valore ?: '-'); ?></td>
                                <td>
                                    <?php if($contact->opt_in): ?>
                                    <span class="badge badge-success">Iscritto</span>
                                    <?php else: ?>
                                    <span class="badge badge-danger">Disiscritto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="rimuoviContatto(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Deseleziona le righe che non vuoi includere nella lista</small>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Salva
            </button>
            <a href="<?php echo e(route('mailing-liste.index')); ?>" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
let contattiCaricati = [];

document.addEventListener('DOMContentLoaded', function() {
    fetch('/glastree/gruppi/all', { credentials: 'include' })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            const select = document.getElementById('gruppi-select');
            data.forEach(function(g) {
                const option = document.createElement('option');
                option.value = g.id;
                option.textContent = g.full_path || g.nome;
                select.appendChild(option);
            });
        })
        .catch(function(err) {
            console.error('Errore gruppi:', err);
        });

    fetch('/glastree/individui/all', { credentials: 'include' })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            const select = document.getElementById('individui-select');
            data.forEach(function(ind) {
                const option = document.createElement('option');
                option.value = ind.id;
                option.textContent = ind.codice_id + ' - ' + ind.cogname + ' ' + ind.nome;
                select.appendChild(option);
            });
        })
        .catch(function(err) {
            console.error('Errore individui:', err);
        });

    contattiCaricati = Array.from(document.querySelectorAll('.contatto-checkbox')).map(cb => ({
        individuo_id: parseInt(cb.value),
        email: cb.dataset.email,
        existing: true
    }));
});

function caricaSelezionati() {
    const gruppiIds = Array.from(document.getElementById('gruppi-select').selectedOptions).map(opt => parseInt(opt.value));
    const individuiIds = Array.from(document.getElementById('individui-select').selectedOptions).map(opt => parseInt(opt.value));

    if (gruppiIds.length === 0 && individuiIds.length === 0) {
        alert('Seleziona almeno un gruppo o un individuo');
        return;
    }

    const promises = [];

    gruppiIds.forEach(function(gruppoId) {
        promises.push(
            fetch('/glastree/gruppi/' + gruppoId + '/individui-email', { credentials: 'include' })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    data.forEach(function(ind) {
                        if (ind.emails && ind.emails.length > 0) {
                            if (!contattiCaricati.find(c => c.individuo_id === ind.id)) {
                                contattiCaricati.push({
                                    individuo_id: ind.id,
                                    codice_id: ind.codice_id,
                                    cognome: ind.cogname,
                                    nome: ind.nome,
                                    email: ind.emails[0],
                                    origini: ['Gruppo']
                                });
                            }
                        }
                    });
                })
        );
    });

    if (individuiIds.length > 0) {
        promises.push(
            fetch('/glastree/individui/email-list?ids=' + individuiIds.join(','), { credentials: 'include' })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    data.forEach(function(ind) {
                        if (ind.emails && ind.emails.length > 0) {
                            if (!contattiCaricati.find(c => c.individuo_id === ind.id)) {
                                contattiCaricati.push({
                                    individuo_id: ind.id,
                                    codice_id: ind.codice_id,
                                    cognome: ind.cogname,
                                    nome: ind.nome,
                                    email: ind.emails[0],
                                    origini: [' Individuo']
                                });
                            }
                        }
                    });
                })
        );
    }

    Promise.all(promises).then(function() {
        renderContattiTable();
    }).catch(function() {
        alert('Errore caricamento contatti');
    });
}

function renderContattiTable() {
    const tbody = document.getElementById('contatti-tbody');
    tbody.innerHTML = '';

    contattiCaricati.forEach(function(c, index) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input contatto-checkbox" id="contatto-${index}" value="${c.individuo_id}" data-email="${c.email}" checked>
                    <label class="custom-control-label" for="contatto-${index}"></label>
                </div>
            </td>
            <td><span class="badge badge-secondary">${c.codice_id || ''}</span></td>
            <td>${c.cognome || ''}</td>
            <td>${c.nome || ''}</td>
            <td>${c.email || ''}</td>
            <td><small class="text-muted">${c.origini ? c.origini.join(', ') : 'Esistente'}</small></td>
            <td>
                <button type="button" class="btn btn-xs btn-outline-danger" onclick="rimuoviContatto(this)">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('contatti-count').textContent = contattiCaricati.length;
}

function rimuoviContatto(btn) {
    btn.closest('tr').remove();
    const count = document.querySelectorAll('.contatto-checkbox').length;
    document.getElementById('contatti-count').textContent = count;
}

function toggleSelectAll(source) {
    document.querySelectorAll('.contatto-checkbox').forEach(function(cb) {
        cb.checked = source.checked;
    });
}

function selezionaTutti() {
    document.querySelectorAll('.contatto-checkbox').forEach(function(cb) {
        cb.checked = true;
    });
    document.getElementById('select-all').checked = true;
}

function deselezionaTutti() {
    document.querySelectorAll('.contatto-checkbox').forEach(function(cb) {
        cb.checked = false;
    });
    document.getElementById('select-all').checked = false;
}

document.getElementById('ml-form').addEventListener('submit', function(e) {
    const selected = [];
    document.querySelectorAll('.contatto-checkbox:checked').forEach(function(cb) {
        selected.push({
            individuo_id: parseInt(cb.value),
            email: cb.dataset.email
        });
    });

    if (selected.length === 0) {
        e.preventDefault();
        alert('Seleziona almeno un contatto con email');
        return;
    }

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'contatti_json';
    input.value = JSON.stringify(selected);
    this.appendChild(input);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/mailing-liste/edit.blade.php ENDPATH**/ ?>