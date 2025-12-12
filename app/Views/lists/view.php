<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <!-- List Header -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <h1 class="mb-3"><?= esc($list['title']) ?></h1>
            <p class="lead text-muted mb-4"><?= esc($list['description']) ?></p>
            
            <!-- Creator Info -->
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <div class="me-4">
                    <a href="<?= base_url('index.php/find/' . urlencode($list['username'])) ?>" class="text-decoration-none">
                        <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                        <div>
                            <strong class="d-block"><?= esc($list['first_name'] . ' ' . $list['last_name']) ?></strong>
                            <small class="text-muted">@<?= esc($list['username']) ?></small>
                        </div>
                    </a>
                </div>
                <div class="me-4">
                    <i class="fas fa-eye text-muted"></i>
                    <span class="text-muted"><?= number_format($list['views']) ?> weergaven</span>
                </div>
                <?php if ($list['category_name']): ?>
                    <div>
                        <span class="badge bg-primary">
                            <i class="fas fa-tag"></i> <?= esc($list['category_name']) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Share Buttons -->
            <div class="mb-4">
                <h6 class="mb-3"><i class="fas fa-share-alt"></i> Deel deze lijst</h6>
                <div class="btn-group" role="group">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" 
                       target="_blank" 
                       class="btn btn-outline-primary" 
                       title="Deel op Facebook">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode($list['title'] . ' - ' . current_url()) ?>" 
                       target="_blank" 
                       class="btn btn-outline-success" 
                       title="Deel op WhatsApp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <button class="btn btn-outline-secondary" 
                            onclick="copyToClipboard('<?= current_url() ?>')" 
                            title="Kopieer lijklink naar klembord">
                        <i class="fas fa-copy"></i> Link Kopiëren
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="mb-5">
        <h3 class="mb-4"><i class="fas fa-gift"></i> Producten in deze lijst</h3>
        
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100 shadow-sm">
                            <!-- Product Image -->
                            <div class="position-relative overflow-hidden" style="height: 220px;">
                                <?php if ($product['image_url']): ?>
                                    <img src="<?= esc($product['image_url']) ?>" 
                                         class="card-img-top w-100 h-100" 
                                         style="object-fit: cover;" 
                                         alt="<?= esc($product['title']) ?>">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <!-- Product Title -->
                                <h5 class="card-title text-truncate" title="<?= esc($product['title']) ?>">
                                    <?= esc(character_limiter($product['title'], 50)) ?>
                                </h5>
                                
                                <!-- Product Description -->
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= esc(character_limiter($product['description'], 80)) ?>
                                </p>
                                
                                <!-- Price -->
                                <?php if ($product['price']): ?>
                                    <div class="mb-3">
                                        <span class="h5 text-primary fw-bold">€<?= number_format($product['price'], 2) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Custom Note -->
                                <?php if ($product['custom_note']): ?>
                                    <div class="alert alert-info alert-sm mb-3" style="font-size: 0.85rem;">
                                        <i class="fas fa-sticky-note"></i> <?= esc($product['custom_note']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Store Source -->
                                <small class="text-muted d-block mb-3">
                                    <i class="fas fa-store"></i> <?= esc($product['source']) ?>
                                </small>
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('index.php/out/' . $product['product_id'] . '?list=' . $list['id']) ?>" 
                                       class="btn btn-primary btn-sm" 
                                       target="_blank"
                                       title="Bekijk product in winkel">
                                        <i class="fas fa-external-link-alt"></i> Product Bekijken
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="copyAffiliateLink('<?= base_url('index.php/out/' . $product['product_id'] . '?list=' . $list['id']) ?>')"
                                            title="Kopieer affiliate link om te delen">
                                        <i class="fas fa-share-alt"></i> Link Delen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <h5>Nog geen producten in deze lijst</h5>
                        <p class="text-muted mb-0">De lijsteigenaar heeft nog geen producten toegevoegd.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Lijklink gekopieerd naar klembord!', 'success');
    }, function(err) {
        showToast('Kan link niet kopiëren. Probeer het opnieuw.', 'error');
        console.error('Could not copy text: ', err);
    });
}

function copyAffiliateLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Create a temporary toast notification
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = '<i class="fas fa-check"></i> Productlink gekopieerd! Deel deze link om anderen te helpen dit product te vinden.';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }, function(err) {
        alert('Kan link niet kopiëren. Probeer het opnieuw.');
        console.error('Could not copy text: ', err);
    });
}
</script>
<?= $this->endSection() ?>
