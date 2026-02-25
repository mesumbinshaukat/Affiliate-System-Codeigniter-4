<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('products/categories') ?>">Categorieën</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($category['name']) ?></li>
                </ol>
            </nav>
            <h1>
                <?php if ($category['icon']): ?>
                    <i class="<?= esc($category['icon']) ?>"></i>
                <?php endif; ?>
                <?= esc($category['name']) ?>
            </h1>
            <?php if ($category['description']): ?>
                <p class="lead text-muted"><?= esc($category['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Products Grid -->
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
                                <p class="h4 text-primary">€<?= number_format($product['price'], 2, ',', '') ?></p>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2 mb-2">
                                <a href="<?= base_url('out/' . $product['id']) ?>" 
                                   class="btn btn-primary flex-grow-1" target="_blank">
                                    Product bekijken <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button class="btn btn-outline-secondary" 
                                        onclick="copyAffiliateLink('<?= base_url('out/' . $product['id']) ?>')"
                                        title="Affiliate-link kopiëren">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            
                            <small class="text-muted d-block">
                                <i class="fas fa-store"></i> <?= esc($product['source']) ?>
                                <?php if (isset($product['list_count']) && $product['list_count'] > 0): ?>
                                    | <i class="fas fa-list"></i> In <?= $product['list_count'] ?> lijst(en)
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Nog geen producten gevonden in deze categorie.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (!empty($products) && isset($pager)): ?>
        <div class="row mt-4">
            <div class="col">
                <?= $pager->links() ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyAffiliateLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Create a temporary toast notification
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = '<i class="fas fa-check"></i> Productlink gekopieerd! Deel deze link zodat anderen dit product ook ontdekken.';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }, function(err) {
        alert('Kopiëren mislukt. Probeer het opnieuw.');
        console.error('Could not copy text: ', err);
    });
}
</script>
<?= $this->endSection() ?>
