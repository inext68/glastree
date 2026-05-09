<?php $__env->startSection('title', 'Dettaglio Individuo'); ?>
<?php $__env->startSection('page_title', 'Dettaglio Individuo'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('individui.index')); ?>">Individui</a></li>
<li class="breadcrumb-item active"><?php echo e($individuo->nome_completo); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user mr-2"></i><?php echo e($individuo->nome_completo); ?></h3>
            </div>
            <div class="card-body">
                <p><strong>Codice:</strong> <span class="badge badge-secondary"><?php echo e($individuo->codice_id); ?></span></p>
                <p><strong>Data di nascita:</strong> <?php echo e($individuo->data_nascita?->format('d/m/Y') ?: '-'); ?></p>
                <p><strong>Genere:</strong> <?php echo e($individuo->genere === 'M' ? 'Maschio' : ($individuo->genere === 'F' ? 'Femmina' : '-')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Residenza</h3></div>
            <div class="card-body">
                <p><strong>Indirizzo:</strong> <?php echo e($individuo->indirizzo ?: '-'); ?></p>
                <p><strong>CAP:</strong> <?php echo e($individuo->cap ?: '-'); ?></p>
                <p><strong>Città:</strong> <?php echo e($individuo->città ?: '-'); ?></p>
                <p><strong>Provincia:</strong> <?php echo e($individuo->sigla_provincia ?: '-'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-id-card mr-2"></i>Documento di identità</h3></div>
            <div class="card-body">
                <p><strong>Tipo:</strong> <?php echo e($individuo->tipo_documento ? ucfirst(str_replace('_', ' ', $individuo->tipo_documento)) : '-'); ?></p>
                <p><strong>Numero:</strong> <?php echo e($individuo->numero_documento ?: '-'); ?></p>
                <p><strong>Scadenza:</strong> <?php echo e($individuo->scadenza_documento?->format('d/m/Y') ?: '-'); ?></p>
                <?php if($individuo->hasDocumentoScaduto()): ?>
                    <p><span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Documento scaduto da <?php echo e($individuo->giorni_scadenza_documento); ?> giorni</span></p>
                <?php elseif($individuo->hasDocumentoScadeEntroGiorni(30)): ?>
                    <p><span class="badge badge-warning"><i class="fas fa-exclamation-circle mr-1"></i> Scade tra <?php echo e($individuo->giorni_scadenza_documento); ?> giorni</span></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($individuo->note): ?>
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Note</h3></div>
            <div class="card-body">
                <p class="mb-0"><?php echo nl2br(e($individuo->note)); ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-address-book mr-2"></i>Contatti</h3>
    </div>
    <div class="card-body p-0">
        <?php if($individuo->contatti->count() > 0): ?>
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 20%;">Tipo</th>
                    <th style="width: 30%;">Valore</th>
                    <th style="width: 20%;">Etichetta</th>
                    <th style="width: 10%;">Primario</th>
                    <th style="width: 80px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $individuo->contatti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contatto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
$protocols = ['http://', 'https://', 'ftp://', 'ssh://', 'sftp://', 'telnet://'];
$isUrl = false;
if (in_array($contatto->tipo, ['web', 'telegram'])) {
    foreach ($protocols as $p) {
        if (str_starts_with(strtolower($contatto->valore), $p)) {
            $isUrl = true;
            break;
        }
    }
}
?>
                <tr id="contatto-row-<?php echo e($contatto->id); ?>">
                    <td><span class="text-capitalize"><?php echo e($contatto->tipo); ?></span></td>
                    <td>
                        <?php if($contatto->tipo === 'email'): ?>
                            <a href="mailto:<?php echo e($contatto->valore); ?>"><?php echo e($contatto->valore); ?></a>
                        <?php elseif(in_array($contatto->tipo, ['telefono', 'cellulare', 'whatsapp'])): ?>
                            <a href="tel:<?php echo e($contatto->valore); ?>"><?php echo e($contatto->valore); ?></a>
                        <?php elseif($isUrl): ?>
                            <a href="<?php echo e($contatto->valore); ?>" target="_blank"><?php echo e($contatto->valore); ?></a>
                        <?php else: ?>
                            <?php echo e($contatto->valore); ?>

                        <?php endif; ?>
                    </td>
                    <td><?php echo e($contatto->etichetta ?: '-'); ?></td>
                    <td>
                        <?php if($contatto->is_primary): ?>
                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Sì</span>
                        <?php else: ?>
                            <span class="text-muted">No</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-warning" onclick="editContattoInline(<?php echo e($contatto->id); ?>)" title="Modifica">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="<?php echo e(url('/contatti/' . $contatto->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <input type="hidden" name="_redirect" value="<?php echo e(url('/individui/' . $individuo->id)); ?>">
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare questo contatto?')" title="Elimina">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="contatto-edit-<?php echo e($contatto->id); ?>" style="display:none;">
                    <td colspan="5">
                        <form action="<?php echo e(url('/contatti/' . $contatto->id)); ?>" method="POST" class="mb-0">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="_redirect" value="<?php echo e(url('/individui/' . $individuo->id)); ?>">
                            <input type="hidden" name="individuo_id" value="<?php echo e($individuo->id); ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="tipo" class="form-control form-control-sm" required>
                                        <option value="telefono" <?php echo e($contatto->tipo === 'telefono' ? 'selected' : ''); ?>>Telefono</option>
                                        <option value="cellulare" <?php echo e($contatto->tipo === 'cellulare' ? 'selected' : ''); ?>>Cellulare</option>
                                        <option value="email" <?php echo e($contatto->tipo === 'email' ? 'selected' : ''); ?>>Email</option>
                                        <option value="fax" <?php echo e($contatto->tipo === 'fax' ? 'selected' : ''); ?>>Fax</option>
                                        <option value="web" <?php echo e($contatto->tipo === 'web' ? 'selected' : ''); ?>>Web</option>
                                        <option value="telegram" <?php echo e($contatto->tipo === 'telegram' ? 'selected' : ''); ?>>Telegram</option>
                                        <option value="whatsapp" <?php echo e($contatto->tipo === 'whatsapp' ? 'selected' : ''); ?>>WhatsApp</option>
                                        <option value="altro" <?php echo e($contatto->tipo === 'altro' ? 'selected' : ''); ?>>Altro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="valore" class="form-control form-control-sm" value="<?php echo e($contatto->valore); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="etichetta" class="form-control form-control-sm" value="<?php echo e($contatto->etichetta); ?>" placeholder="Etichetta">
                                </div>
                                <div class="col-md-1 text-center">
                                    <input type="checkbox" name="is_primary" value="1" <?php echo e($contatto->is_primary ? 'checked' : ''); ?>>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-check mr-1"></i>Salva</button>
                                    <button type="button" class="btn btn-xs btn-secondary" onclick="cancelEditContatto(<?php echo e($contatto->id); ?>)"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="fas fa-address-book fa-2x mb-2"></i>
            <p class="mb-0">Nessun contatto</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder mr-2"></i>Gruppi</h3>
        <button type="button" class="btn btn-xs btn-success float-right" onclick="showGruppoForm()">
            <i class="fas fa-plus mr-1"></i> Aggiungi
        </button>
    </div>
    <div class="card-body p-0">
        
        <?php if($individuo->gruppi->count() > 0): ?>
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Nome</th>
                    <th>Diocesi</th>
                    <th>Responsabile</th>
                    <th style="width: 130px;">Ruolo</th>
                    <th style="width: 110px;">Data Adesione</th>
                    <th style="width: 80px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $individuo->gruppi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gruppo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr id="gruppo-row-<?php echo e($gruppo->id); ?>">
                    <td>
                        <i class="fas fa-folder text-warning mr-1"></i>
                        <a href="<?php echo e(url('/gruppi/' . $gruppo->id)); ?>"><?php echo e($gruppo->nome); ?></a>
                    </td>
                    <td><?php echo e($gruppo->diocesi?->nome ?: '-'); ?></td>
                    <td><?php echo e($gruppo->responsabile?->nome_completo ?? '-'); ?></td>
                    <td><?php echo e($gruppo->pivot->ruolo_nel_gruppo ?: '-'); ?></td>
                    <td><?php echo e($gruppo->pivot->data_adesione ? \Carbon\Carbon::parse($gruppo->pivot->data_adesione)->format('d/m/Y') : '-'); ?></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-warning" onclick="editGruppoInline(<?php echo e($gruppo->id); ?>)" title="Modifica ruolo">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger" onclick="deleteGruppo(<?php echo e($gruppo->id); ?>)" title="Rimuovi">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr id="gruppo-edit-<?php echo e($gruppo->id); ?>" style="display:none;">
                    <td colspan="6">
                        <form action="<?php echo e(url('/individui/' . $individuo->id . '/gruppi/' . $gruppo->id)); ?>" method="POST" class="mb-0">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" value="<?php echo e($gruppo->nome); ?>" disabled>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="ruolo_nel_gruppo" class="form-control form-control-sm" value="<?php echo e($gruppo->pivot->ruolo_nel_gruppo); ?>" placeholder="Ruolo">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="data_adesione" class="form-control form-control-sm" value="<?php echo e($gruppo->pivot->data_adesione); ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-check mr-1"></i>Salva</button>
                                    <button type="button" class="btn btn-xs btn-secondary" onclick="cancelEditGruppo(<?php echo e($gruppo->id); ?>)"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="fas fa-users fa-2x mb-2"></i>
            <p class="mb-0">Nessun gruppo associato</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-3" id="gruppo-add-card" style="display:none; background-color: #fff3cd; border-color: #ffc107;">
    <div class="card-header bg-warning">
        <h3 class="card-title"><i class="fas fa-folder-plus mr-2"></i>Aggiungi a Gruppo</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <select id="new-gruppo-id" class="form-control form-control-sm">
                    <option value="">Seleziona gruppo...</option>
                    <?php $__currentLoopData = \App\Models\Gruppo::orderBy('nome')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$individuo->gruppi->contains($g->id)): ?>
                            <option value="<?php echo e($g->id); ?>"><?php echo e($g->full_path); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" id="new-gruppo-ruolo" class="form-control form-control-sm" placeholder="Ruolo">
            </div>
            <div class="col-md-3">
                <input type="date" id="new-gruppo-data" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-sm" onclick="submitGruppoForm()">
                    <i class="fas fa-check mr-1"></i> Associa
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="hideGruppoForm()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file mr-2"></i>Documenti</h3>
        <button type="button" class="btn btn-xs btn-success float-right" onclick="showDocumentoForm()">
            <i class="fas fa-plus mr-1"></i> Carica
        </button>
    </div>
    <div class="card-body p-0">
        <div id="documento-add-form" style="display:none;" class="p-3 bg-light border-bottom">
            <form action="<?php echo e(url('/documenti')); ?>" method="POST" enctype="multipart/form-data" class="mb-0">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="visibilita" value="individuo">
                <input type="hidden" name="visibilita_target_id" value="<?php echo e($individuo->id); ?>">
                <input type="hidden" name="visibilita_target_type" value="App\Models\Individuo">
                <input type="hidden" name="redirect_to" value="<?php echo e(url('/individui/' . $individuo->id)); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="nome_file" class="form-control form-control-sm" placeholder="Nome documento" required>
                    </div>
                    <div class="col-md-3">
                        <select name="tipologia" class="form-control form-control-sm" required>
                            <option value="">Tipologia...</option>
                            <option value="documento">Documento</option>
                            <option value="avatar">Avatar</option>
                            <option value="galleria">Galleria</option>
                            <option value="statuto">Statuto</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-xs btn-success"><i class="fas fa-upload mr-1"></i>Carica</button>
                        <button type="button" class="btn btn-xs btn-secondary" onclick="hideDocumentoForm()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <?php if($individuo->documenti->count() > 0): ?>
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Nome</th>
                    <th style="width: 120px;">Tipologia</th>
                    <th style="width: 80px;">Dimensione</th>
                    <th style="width: 130px;">Data Upload</th>
                    <th style="width: 80px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $individuo->documenti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                            <button type="button" class="btn btn-xs btn-primary" onclick="previewDocumento(<?php echo e($documento->id); ?>, '<?php echo e($documento->mime_type); ?>')" title="Anteprima">
                                <i class="fas fa-eye"></i>
                            </button>
                        <?php endif; ?>
                        <form action="<?php echo e(url('/documenti/' . $documento->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <input type="hidden" name="_redirect" value="<?php echo e(url('/individui/' . $individuo->id)); ?>">
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Eliminare questo documento?')" title="Elimina">
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
            <i class="fas fa-file fa-2x mb-2"></i>
            <p class="mb-0">Nessun documento</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo e(url('/individui/' . $individuo->id . '/edit')); ?>" class="btn btn-warning">
        <i class="fas fa-edit mr-1"></i> Modifica
    </a>
    <form action="<?php echo e(url('/individui/' . $individuo->id)); ?>" method="POST" class="d-inline">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Confermi l\'eliminazione di <?php echo e($individuo->nome_completo); ?>?')">
            <i class="fas fa-trash mr-1"></i> Elimina
        </button>
    </form>
    <a href="<?php echo e(route('individui.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Torna all'elenco
    </a>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 90vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file mr-2"></i><span id="previewModalTitle">Anteprima</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0" style="min-height: 400px;">
                <iframe id="previewFrame" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <a id="previewDownloadBtn" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download mr-1"></i> Scarica
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function editContattoInline(id) {
    document.getElementById('contatto-row-' + id).style.display = 'none';
    document.getElementById('contatto-edit-' + id).style.display = 'table-row';
}

function cancelEditContatto(id) {
    document.getElementById('contatto-row-' + id).style.display = 'table-row';
    document.getElementById('contatto-edit-' + id).style.display = 'none';
}

function showGruppoForm() {
    var card = document.getElementById('gruppo-add-card');
    if (card) {
        card.style.display = 'block';
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function hideGruppoForm() {
    var card = document.getElementById('gruppo-add-card');
    if (card) {
        card.style.display = 'none';
    }
}

function submitGruppoForm() {
    var gruppoId = document.getElementById('new-gruppo-id').value;
    var ruolo = document.getElementById('new-gruppo-ruolo').value;
    var dataAdesione = document.getElementById('new-gruppo-data').value;
    
    if (!gruppoId) {
        alert('Seleziona un gruppo');
        return;
    }
    
    var formData = new URLSearchParams();
    formData.append('gruppo_id', gruppoId);
    formData.append('ruolo_nel_gruppo', ruolo);
    formData.append('data_adesione', dataAdesione);
    
    fetch('<?php echo e(url('/individui/' . $individuo->id . '/gruppi')); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            hideGruppoForm();
            window.location.reload();
        } else {
            alert(data.error || 'Errore');
        }
    })
    .catch(error => {
        alert('Errore: ' + error.message);
    });
}

function editGruppoInline(id) {
    document.getElementById('gruppo-row-' + id).style.display = 'none';
    document.getElementById('gruppo-edit-' + id).style.display = 'table-row';
}

function cancelEditGruppo(id) {
    document.getElementById('gruppo-row-' + id).style.display = 'table-row';
    document.getElementById('gruppo-edit-' + id).style.display = 'none';
}

function deleteGruppo(gruppoId) {
    if (!confirm('Rimuovere da questo gruppo?')) return;
    
    fetch('<?php echo e(url('/individui/' . $individuo->id . '/gruppi')); ?>/' + gruppoId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Errore');
        }
    })
    .catch(error => {
        alert('Errore: ' + error.message);
    });
}

function showDocumentoForm() {
    document.getElementById('documento-add-form').style.display = 'block';
}

function hideDocumentoForm() {
    document.getElementById('documento-add-form').style.display = 'none';
}

function previewDocumento(id, mimeType) {
    var previewUrl = '<?php echo e(url('/documenti/')); ?>/' + id + '/preview';
    var downloadUrl = '<?php echo e(url('/documenti/')); ?>/' + id + '/download';

    document.getElementById('previewFrame').src = previewUrl;
    document.getElementById('previewDownloadBtn').href = downloadUrl;

    if (mimeType && mimeType.startsWith('image/')) {
        document.getElementById('previewDownloadBtn').style.display = 'inline-block';
    }

    $('#previewModal').modal('show');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlte', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/glastree/resources/views/individui/show.blade.php ENDPATH**/ ?>