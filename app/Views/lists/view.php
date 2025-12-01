<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <!-- List Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1><?= esc($list['title']) ?></h1>
            <p class="lead text-muted"><?= esc($list['description']) ?></p>
            
            <div class="d-flex align-items-center mb-3">
                <div class="me-4">
                    <i class="fas fa-user"></i>
                    <strong><?= esc($list['first_name'] . ' ' . $list['last_name']) ?></strong>
                    <small class="text-muted">@<?= esc($list['username']) ?></small>
                </div>
                <div class="me-4">
                    <i class="fas fa-eye"></i> <?= number_format($list['views']) ?> views
                </div>
                <?php if ($list['category_name']): ?>
                    <div>
                        <a href="<?= base_url('category/' . $list['category_slug']) ?>" class="badge bg-secondary text-decoration-none">
                            <?= esc($list['category_name']) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Share Buttons -->
            <div class="mb-4">
                <strong>Share:</strong>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="https://wa.me/?text=<?= urlencode($list['title'] . ' - ' . current_url()) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('<?= current_url() ?>')">
                    <i class="fas fa-copy"></i> Copy Link
                </button>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['image_url']): ?>
                            <img src="<?= esc($product['image_url']) ?>" class="card-img-top" alt="<?= esc($product['title']) ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($product['title']) ?></h5>
                            <p class="card-text text-muted"><?= esc(character_limiter($product['description'], 100)) ?></p>
                            
                            <?php if ($product['price']): ?>
                                <p class="h4 text-primary">€<?= number_format($product['price'], 2) ?></p>
                            <?php endif; ?>
                            
                            <?php if ($product['custom_note']): ?>
                                <div class="alert alert-info">
                                    <small><?= esc($product['custom_note']) ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?= base_url('out/' . $product['product_id'] . '?list=' . $list['id']) ?>" 
                               class="btn btn-primary w-100" target="_blank">
                                View Product <i class="fas fa-external-link-alt"></i>
                            </a>
                            
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-store"></i> <?= esc($product['source']) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No products in this list yet.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
<?= $this->endSection() ?>
