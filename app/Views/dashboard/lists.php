<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>My Lists</h1>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('dashboard/list/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New List
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Views</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lists)): ?>
                    <?php foreach ($lists as $list): ?>
                        <tr>
                            <td>
                                <strong><?= esc($list['title']) ?></strong>
                            </td>
                            <td><?= esc($list['category_name'] ?? 'Uncategorized') ?></td>
                            <td>
                                <?php if ($list['status'] === 'published'): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php elseif ($list['status'] === 'draft'): ?>
                                    <span class="badge bg-warning">Draft</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Private</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $list['product_count'] ?? 0 ?></td>
                            <td><?= number_format($list['views']) ?></td>
                            <td><?= date('M d, Y', strtotime($list['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('dashboard/list/edit/' . $list['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($list['status'] === 'published'): ?>
                                        <a href="<?= base_url('list/' . $list['slug']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('dashboard/list/delete/' . $list['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this list?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No lists yet. <a href="<?= base_url('dashboard/list/create') ?>">Create your first list</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
