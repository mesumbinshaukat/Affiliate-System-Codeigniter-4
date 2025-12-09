<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1><?= esc($user['first_name'] . ' ' . $user['last_name']) ?>'s Lists</h1>
            <p class="text-muted">@<?= esc($user['username']) ?></p>
        </div>
    </div>

    <div class="row">
        <?php if (!empty($lists)): ?>
            <?php foreach ($lists as $list): ?>
                <div class="col-md-4 mb-4">
                    <div class="card list-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="text-decoration-none text-dark">
                                    <?= esc($list['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted"><?= esc(character_limiter($list['description'], 100)) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($list['created_at'])) ?>
                                </small>
                                <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="btn btn-sm btn-primary">
                                    View List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    This user hasn't published any lists yet.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
