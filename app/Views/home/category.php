<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="mb-4">
        <h1>
            <i class="fas fa-<?= $category['icon'] ?? 'folder' ?>"></i>
            <?= esc($category['name']) ?>
        </h1>
        <?php if ($category['description']): ?>
            <p class="text-muted"><?= esc($category['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if (!empty($lists)): ?>
            <?php foreach ($lists as $list): ?>
                <div class="col-md-4 mb-4">
                    <div class="card list-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= base_url('list/' . $list['slug']) ?>" class="text-decoration-none text-dark">
                                    <?= esc($list['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted"><?= esc(character_limiter($list['description'], 100)) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?= esc($list['username']) ?>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-eye"></i> <?= number_format($list['views']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No lists found in this category yet.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pager)): ?>
        <div class="mt-4">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
