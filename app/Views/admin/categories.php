<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Categorieën Beheren</h1>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('admin/category/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Categorie Maken
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naam</th>
                            <th>Slug</th>
                            <th>Pictogram</th>
                            <th>Status</th>
                            <th>Lijsten</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= esc($category['name']) ?></td>
                                <td><?= esc($category['slug']) ?></td>
                                <td><i class="fas fa-<?= $category['icon'] ?? 'folder' ?>"></i></td>
                                <td>
                                    <span class="badge bg-<?= $category['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= esc($category['status']) ?>
                                    </span>
                                </td>
                                <td><?= $category['list_count'] ?? 0 ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/category/edit/' . $category['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/category/delete/' . $category['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Weet u zeker dat u deze categorie wilt verwijderen?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
