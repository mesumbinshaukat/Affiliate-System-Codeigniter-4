<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Create & Share Product Lists</h1>
                <p class="lead mb-4">Curate your favorite products and share them with the world. Earn through affiliate links.</p>
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= base_url('index.php/register') ?>" class="btn btn-light btn-lg">Get Started</a>
                <?php else: ?>
                    <a href="<?= base_url('index.php/dashboard/list/create') ?>" class="btn btn-light btn-lg">Create a List</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Categories -->
<div class="container my-5">
    <h2 class="mb-4">Browse by Category</h2>
    <div class="d-flex flex-wrap">
        <?php foreach ($categories as $category): ?>
            <a href="<?= base_url('index.php/category/' . $category['slug']) ?>" class="category-badge">
                <i class="fas fa-<?= $category['icon'] ?? 'folder' ?>"></i>
                <?= esc($category['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Lists -->
<?php if (!empty($featuredLists)): ?>
<div class="container my-5">
    <h2 class="mb-4">Featured Lists</h2>
    <div class="row">
        <?php foreach ($featuredLists as $list): ?>
            <div class="col-md-4 mb-4">
                <div class="card list-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="text-decoration-none text-dark">
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
                        <?php if ($list['category_name']): ?>
                            <span class="badge bg-secondary mt-2"><?= esc($list['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Trending Lists -->
<?php if (!empty($trendingLists)): ?>
<div class="container my-5">
    <h2 class="mb-4">Trending Lists</h2>
    <div class="row">
        <?php foreach ($trendingLists as $list): ?>
            <div class="col-md-4 mb-4">
                <div class="card list-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="text-decoration-none text-dark">
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
                        <?php if ($list['category_name']): ?>
                            <span class="badge bg-secondary mt-2"><?= esc($list['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Recent Lists -->
<div class="container my-5">
    <h2 class="mb-4">Recent Lists</h2>
    <div class="row">
        <?php if (!empty($recentLists)): ?>
            <?php foreach ($recentLists as $list): ?>
                <div class="col-md-4 mb-4">
                    <div class="card list-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="text-decoration-none text-dark">
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
                            <?php if ($list['category_name']): ?>
                                <span class="badge bg-secondary mt-2"><?= esc($list['category_name']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted">No lists available yet. Be the first to create one!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
