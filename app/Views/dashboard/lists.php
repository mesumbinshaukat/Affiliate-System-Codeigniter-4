<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Mijn Lijsten</h1>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('index.php/dashboard/list/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nieuwe Lijst
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Categorie</th>
                    <th>Status</th>
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
                            </td>
                            <td><?= esc($list['category_name'] ?? 'Zonder categorie') ?></td>
                            <td>
                                <?php if ($list['status'] === 'published'): ?>
                                    <span class="badge bg-success">Gepubliceerd</span>
                                <?php elseif ($list['status'] === 'draft'): ?>
                                    <span class="badge bg-warning">Concept</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Privé</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $list['product_count'] ?? 0 ?></td>
                            <td><?= number_format($list['views']) ?></td>
                            <td><?= date('M d, Y', strtotime($list['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('index.php/dashboard/list/edit/' . $list['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($list['status'] === 'published'): ?>
                                        <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('index.php/dashboard/list/delete/' . $list['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Weet u zeker dat u deze lijst wilt verwijderen?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Nog geen lijsten. <a href="<?= base_url('index.php/dashboard/list/create') ?>">Maak uw eerste lijst</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
