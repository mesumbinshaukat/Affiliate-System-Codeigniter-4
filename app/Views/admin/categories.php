<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Manage Categories</h1>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('index.php/admin/category/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Category
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
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Icon</th>
                            <th>Status</th>
                            <th>Lists</th>
                            <th>Actions</th>
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
                                        <a href="<?= base_url('index.php/admin/category/edit/' . $category['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('index.php/admin/category/delete/' . $category['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this category?')">
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
