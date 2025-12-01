<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Affiliate Sources</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>API Endpoint</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sources as $source): ?>
                            <tr>
                                <td><?= esc($source['name']) ?></td>
                                <td><?= esc($source['slug']) ?></td>
                                <td><?= esc($source['api_endpoint']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $source['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= esc($source['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/affiliate-source/toggle/' . $source['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        Toggle Status
                                    </a>
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
