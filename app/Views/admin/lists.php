<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Manage Lists</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lists as $list): ?>
                            <tr>
                                <td><?= $list['id'] ?></td>
                                <td><?= esc(character_limiter($list['title'], 40)) ?></td>
                                <td><?= esc($list['username']) ?></td>
                                <td><?= esc($list['category_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-<?= $list['status'] === 'published' ? 'success' : ($list['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                        <?= esc($list['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/list/featured/' . $list['id']) ?>" class="btn btn-sm btn-<?= $list['is_featured'] ? 'warning' : 'outline-secondary' ?>">
                                        <i class="fas fa-star"></i>
                                    </a>
                                </td>
                                <td><?= number_format($list['views']) ?></td>
                                <td><?= date('M d, Y', strtotime($list['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if ($list['status'] === 'published'): ?>
                                            <a href="<?= base_url('list/' . $list['slug']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/list/delete/' . $list['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this list?')">
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
