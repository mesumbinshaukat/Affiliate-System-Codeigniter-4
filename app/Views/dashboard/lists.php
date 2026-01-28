<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Mijn Lijsten</h1>
            <p class="text-muted">Lijsten die u bezit en lijsten waar u aan meewerkt</p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('dashboard/list/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nieuwe Lijst
            </a>
        </div>
    </div>

    <div class="glass-card p-3 mb-4">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Categorie</th>
                        <th>Status</th>
                        <th>Rol</th>
                        <th>Producten</th>
                        <th>Weergaven</th>
                        <th>Gemaakt op</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lists)): ?>
                        <?php foreach ($lists as $list): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($list['title']) ?></strong>
                                    <?php if (!empty($list['is_collaboration'])): ?>
                                        <span class="badge-soft info ms-2" title="U bent medewerker aan deze lijst">
                                            <i class="fas fa-users"></i> Samenwerking
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($list['category_name'] ?? 'Zonder categorie') ?></td>
                                <td>
                                    <?php if ($list['status'] === 'published'): ?>
                                        <span class="badge-soft success">Gepubliceerd</span>
                                    <?php elseif ($list['status'] === 'draft'): ?>
                                        <span class="badge-soft warning">Concept</span>
                                    <?php else: ?>
                                        <span class="badge-soft muted">Privé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($list['is_collaboration'])): ?>
                                        <span class="text-muted"><i class="fas fa-user-edit"></i> Medewerker</span>
                                    <?php else: ?>
                                        <span class="text-primary"><i class="fas fa-crown"></i> Eigenaar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $list['product_count'] ?? 0 ?></td>
                                <td><?= number_format($list['views']) ?></td>
                                <td><?= date('M d, Y', strtotime($list['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('dashboard/list/edit/' . $list['id']) ?>" class="btn btn-sm btn-outline-primary" title="Bewerken">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($list['status'] === 'published'): ?>
                                            <a href="<?= base_url('list/' . $list['slug']) ?>" class="btn btn-sm btn-outline-info" target="_blank" title="Bekijken">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (empty($list['is_collaboration'])): ?>
                                            <a href="<?= base_url('dashboard/list/delete/' . $list['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Weet u zeker dat u deze lijst wilt verwijderen?')"
                                               title="Verwijderen">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-warning" 
                                                    onclick="leaveCollaboration(<?= $list['id'] ?>)"
                                                    title="Verlaten">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Nog geen lijsten. <a href="<?= base_url('dashboard/list/create') ?>">Maak uw eerste lijst</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function leaveCollaboration(listId) {
    if (!confirm('Weet u zeker dat u deze samenwerking wilt verlaten? U verliest toegang tot het bewerken van deze lijst.')) {
        return;
    }
    
    fetch('<?= base_url('collaboration/leave') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            list_id: listId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('U heeft de samenwerking succesvol verlaten');
            location.reload();
        } else {
            alert('Fout: ' + (data.message || 'Kon samenwerking niet verlaten'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden bij het verlaten van de samenwerking');
    });
}
</script>

<?= $this->endSection() ?>
