<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Browse by Category</h1>
            <p class="text-muted">Choose a category to explore products</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <a href="<?= base_url('products') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body">
                    <i class="fas fa-th fa-4x text-primary mb-3"></i>
                    <h4>All Products</h4>
                    <p class="text-muted">Browse all available products</p>
                </div>
            </a>
        </div>
        
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4 mb-3">
                    <a href="<?= base_url('products/category/' . $category['slug']) ?>" 
                       class="card text-center text-decoration-none h-100">
                        <div class="card-body">
                            <?php if ($category['icon']): ?>
                                <i class="<?= esc($category['icon']) ?> fa-4x text-primary mb-3"></i>
                            <?php else: ?>
                                <i class="fas fa-folder fa-4x text-primary mb-3"></i>
                            <?php endif; ?>
                            <h4><?= esc($category['name']) ?></h4>
                            <?php if ($category['description']): ?>
                                <p class="text-muted"><?= esc($category['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No categories available yet.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
