<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .list-hero {
        background: linear-gradient(135deg, #fdf2f8, #e0f2fe);
        border-radius: 24px;
        padding: 32px;
        border: 1px solid #f0f4ff;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .view-toggle {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }

    .view-toggle .btn {
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s ease;
    }

    .view-toggle .btn.active {
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        border-color: #2563eb;
        color: #fff;
    }

    .view-toggle .btn:hover:not(.active) {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .creator-info {
        background: linear-gradient(135deg, #101D4A, #1F3B7A);
        border-radius: 26px;
        padding: 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(9, 12, 34, 0.25);
    }

    .creator-info::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 45%);
        opacity: 0.6;
        pointer-events: none;
    }

    .creator-info__inner {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        z-index: 2;
        align-items: center;
        justify-content: space-between;
    }

    .creator-info__details {
        max-width: 420px;
    }

    .creator-eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.25em;
        font-size: 0.78rem;
        color: rgba(255,255,255,0.72);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .creator-info__name {
        font-size: clamp(1.7rem, 3.5vw, 2.2rem);
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .creator-info__username {
        color: rgba(255,255,255,0.85);
        font-weight: 600;
    }

    .creator-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .creator-actions .btn-outline-light {
        border-radius: 14px;
        border-width: 2px;
        font-weight: 600;
    }

    .creator-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .creator-stat {
        background: rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 1rem 1.25rem;
        min-width: 150px;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.15);
    }

    .creator-stat__label {
        font-size: 0.8rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.7);
        margin-bottom: 0.25rem;
    }

    .creator-stat__value {
        font-size: 1.35rem;
        font-weight: 700;
    }

    .list-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
    }

    .list-product-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .list-product-list .list-product-card {
        flex-direction: row;
        min-height: auto;
        max-width: 100%;
        width: 100%;
    }

    .list-product-list .list-product-card__media {
        min-width: 180px;
        max-width: 180px;
        min-height: 180px;
        flex-shrink: 0;
    }

    .list-product-list .list-product-card__body {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }

    .list-product-list .list-product-card__actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }

    .list-product-list .list-product-card__actions .btn {
        flex: 0 1 auto;
        white-space: nowrap;
    }

    .list-product-list .list-product-card__desc {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .list-product-list .list-product-card__title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .list-product-card {
        border: none;
        border-radius: 22px;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12);
        overflow: hidden;
        background: #fff;
        display: flex;
        flex-direction: column;
        min-height: 380px;
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .list-product-card[role="link"] {
        cursor: pointer;
    }

    .list-product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 26px 45px rgba(15, 23, 42, 0.18);
    }

    .list-product-card__media {
        background: linear-gradient(135deg, #eff6ff, #eef2ff);
        min-height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .list-product-card__media img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 14px;
    }

    .list-product-card__body {
        padding: 20px 22px 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .list-product-card__pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 999px;
        background: #ecfeff;
        color: #0369a1;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .list-product-card__title {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        margin-top: 14px;
        margin-bottom: 10px;
        min-height: 48px;
    }

    .list-product-card__desc {
        font-size: 0.9rem;
        color: #475569;
        flex: 1;
    }

    .list-product-card__price {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0ea5e9;
        margin: 14px 0;
    }

    .list-product-card__note {
        background: #fef9c3;
        border: 1px dashed #fbbf24;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.85rem;
        color: #92400e;
        margin-bottom: 12px;
    }

    .list-product-card__actions .btn {
        border-radius: 12px;
        font-weight: 600;
        position: relative;
        z-index: 2;
    }

    .list-product-card__actions {
        gap: 10px;
    }

    .btn-product-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        border-radius: 14px;
        padding: 10px 14px;
        font-weight: 700;
        text-transform: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.15);
    }

    .btn-product-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.18);
    }

    .btn-product-action--primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
    }

    .btn-product-action--primary:hover {
        color: #fff;
        background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
    }

    .btn-product-action--purchase {
        background: linear-gradient(135deg, #f97316, #facc15);
        color: #7c2d12;
        border: 1px solid rgba(249, 115, 22, 0.4);
        box-shadow: 0 8px 18px rgba(249, 115, 22, 0.3);
    }

    .btn-product-action--undo {
        background: linear-gradient(135deg, #10b981, #22c55e);
        color: #064e3b;
        border: 1px solid rgba(16, 185, 129, 0.4);
        box-shadow: 0 10px 22px rgba(16, 185, 129, 0.35);
    }

    .btn-product-action--link {
        background: #fff;
        color: #1f2937;
        border: 1px solid #d1d5db;
    }

    .btn-product-action--link:hover {
        background: #f8fafc;
    }

    .list-product-card--claimed .btn-product-action {
        opacity: 1;
        filter: none;
    }
    .list-product-card--claimed .list-product-card__media {
        opacity: 0.55;
    }

    .list-product-card--claimed .list-product-card__body > *:not(.list-product-card__actions) {
        opacity: 0.55;
    }

    .list-product-card--claimed {
        position: relative;
    }

    .list-product-card--claimed::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(148, 163, 184, 0.1) 10px,
            rgba(148, 163, 184, 0.1) 20px
        );
        pointer-events: none;
        border-radius: 22px;
    }

    .claimed-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        z-index: 10;
    }

    .list-product-card__title--claimed {
        text-decoration: line-through;
        color: #94a3b8;
    }

    .section-group {
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid #e2e8f0;
        display: flex;
        align-items: center;
    }

    .section-title i {
        color: #3b82f6;
    }

    @media (max-width: 767px) {
        .list-hero {
            padding: 24px;
        }

        .creator-info__inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .view-toggle {
            flex-direction: column;
        }

        .view-toggle .btn {
            width: 100%;
        }

        .list-product-list .list-product-card {
            flex-direction: column;
        }

        .list-product-list .list-product-card__media {
            min-width: 100%;
            max-width: 100%;
            min-height: 200px;
        }

        .list-product-list .list-product-card__body {
            padding: 16px;
        }

        .list-product-list .list-product-card__actions {
            flex-direction: column;
            gap: 8px;
        }

        .list-product-list .list-product-card__actions .btn {
            width: 100%;
        }

        .list-product-grid {
            grid-template-columns: 1fr;
        }
    }

    
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <?php if (!empty($requiresAccess)): ?>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3"><i class="fas fa-lock me-2 text-primary"></i> Deze lijst is beveiligd</h2>
                        <p class="text-muted mb-4">
                            De eigenaar heeft deze lijst beschermd. Vul de juiste informatie in om toegang te krijgen tot de producten.
                        </p>

                        <?php if (!empty($accessError)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= esc($accessError) ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('list/' . ($accessSlug ?? $list['slug']) . '/access') ?>" method="post" class="mt-3">
                            <?= csrf_field() ?>
                            <?php if (($list['protection_type'] ?? 'password') === 'password'): ?>
                                <div class="mb-3">
                                    <label for="protection_password" class="form-label">Wachtwoord</label>
                                    <input type="password" name="protection_password" id="protection_password" class="form-control form-control-lg" required>
                                </div>
                            <?php elseif (($list['protection_type'] ?? 'question') === 'question'): ?>
                                <div class="mb-3">
                                    <label class="form-label">Beveiligingsvraag</label>
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <strong><?= esc($list['protection_question']) ?></strong>
                                    </div>
                                    <input type="text" name="protection_answer" class="form-control form-control-lg" placeholder="Uw antwoord" required>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-unlock-alt me-1"></i> Toegang aanvragen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
    <!-- List Header -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <h1 class="mb-3"><?= esc($list['title']) ?></h1>
            <p class="lead text-muted mb-4"><?= esc($list['description']) ?></p>
            
            <!-- Creator Info -->
            <div class="creator-info mb-4">
                <div class="creator-info__inner">
                    <div class="creator-info__details">
                        <div class="creator-eyebrow">Lijst Beheerder</div>
                        <h2 class="creator-info__name"><?= esc($list['first_name'] . ' ' . $list['last_name']) ?></h2>
                        <div class="creator-info__username">@<?= esc($list['username']) ?></div>
                        <div class="creator-actions mt-3">
                            <a href="<?= base_url('index.php/find/' . urlencode($list['username'])) ?>" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-user"></i> Bekijk profiel
                            </a>
                            <a href="<?= base_url('index.php/find/' . urlencode($list['username'])) ?>#lists" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list"></i> Meer lijsten
                            </a>
                        </div>
                    </div>
                    <div class="creator-stats">
                        <div class="creator-stat">
                            <div class="creator-stat__label">Weergaven</div>
                            <div class="creator-stat__value"><?= number_format($list['views']) ?></div>
                        </div>
                        <?php if ($list['category_name']): ?>
                        <div class="creator-stat">
                            <div class="creator-stat__label">Categorie</div>
                            <div class="creator-stat__value" style="font-size:1rem; text-transform:capitalize;">
                                <?= esc($list['category_name']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0"><i class="fas fa-gift"></i> Producten in deze lijst</h3>
            <div class="view-toggle">
                <button class="btn active" onclick="switchView('grid')" id="gridViewBtn" title="Rasterweergave">
                    <i class="fas fa-th"></i> Raster
                </button>
                <button class="btn" onclick="switchView('list')" id="listViewBtn" title="Lijstweergave">
                    <i class="fas fa-list"></i> Lijst
                </button>
            </div>
        </div>
        
        <div id="productsContainer">
            <?php if (!empty($products)): ?>
                <?php 
                // Group products by section
                $groupedProducts = [];
                $noSectionProducts = [];
                
                foreach ($products as $product) {
                    if (!empty($product['section_id'])) {
                        $sectionId = $product['section_id'];
                        if (!isset($groupedProducts[$sectionId])) {
                            $groupedProducts[$sectionId] = [
                                'title' => $product['section_title'],
                                'position' => $product['section_position'],
                                'products' => []
                            ];
                        }
                        $groupedProducts[$sectionId]['products'][] = $product;
                    } else {
                        $noSectionProducts[] = $product;
                    }
                }
                
                // Sort sections by position
                uasort($groupedProducts, function($a, $b) {
                    return $a['position'] <=> $b['position'];
                });
                ?>
                
                <?php foreach ($groupedProducts as $sectionId => $section): ?>
                    <div class="section-group mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-folder-open me-2"></i>
                            <?= esc($section['title']) ?>
                        </h3>
                        <div class="list-product-grid">
                            <?php foreach ($section['products'] as $product): ?>
                                <?php 
                                    $isClaimed = !empty($product['claimed_at']);
                                    $productUrl = base_url('index.php/out/' . $product['product_id'] . '?list=' . $list['id'] . '&lp=' . $product['list_product_id']);
                                ?>
                                <div class="list-product-card <?= $isClaimed ? 'list-product-card--claimed' : '' ?>"
                                     data-list-product-id="<?= $product['list_product_id'] ?>"
                                     data-affiliate-url="<?= esc($productUrl) ?>"
                                     role="link"
                                     tabindex="0">
                        <?php if ($isClaimed): ?>
                            <span class="claimed-badge">
                                <i class="fas fa-check-circle"></i> Gekocht
                            </span>
                        <?php endif; ?>
                        <div class="list-product-card__media">
                            <?php if ($product['image_url']): ?>
                                <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($product['title']) ?>">
                            <?php else: ?>
                                <div class="text-muted text-center">
                                    <i class="fas fa-image fa-2x"></i>
                                    <p class="small mt-2 mb-0">Geen afbeelding</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="list-product-card__body">
                            <span class="list-product-card__pill">
                                <i class="fas fa-heart text-danger"></i>
                                Favoriet
                            </span>
                            <h5 class="list-product-card__title <?= $isClaimed ? 'list-product-card__title--claimed' : '' ?>">
                                <?= esc(character_limiter($product['title'], 60)) ?>
                            </h5>
                            <p class="list-product-card__desc">
                                <?= esc(character_limiter($product['description'], 90)) ?>
                            </p>
                            <?php if ($product['price']): ?>
                                <div class="list-product-card__price">
                                    €<?= number_format($product['price'], 2) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['custom_note']): ?>
                                <div class="list-product-card__note">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    <?= esc($product['custom_note']) ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted mb-3 d-block">
                                <i class="fas fa-store me-1"></i> <?= esc($product['source']) ?>
                            </small>
                            
                            <!-- Group Gift Contribution UI -->
                            <?= view('partials/group_gift_contribution', ['product' => $product]) ?>
                            
                            <div class="list-product-card__actions d-grid gap-2">
                                <a href="<?= $productUrl ?>" 
                                   class="btn-product-action btn-product-action--primary w-100" 
                                   target="_blank"
                                   title="Bekijk product in winkel">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>Product Bekijken</span>
                                </a>
                                <?php if (!empty($list['is_crossable'])): ?>
                                    <?php if ($isClaimed): ?>
                                        <button class="btn-product-action btn-product-action--undo w-100"
                                                onclick="undoPurchase(event, <?= $product['list_product_id'] ?>, <?= $list['id'] ?>)"
                                                title="Maak dit item opnieuw beschikbaar">
                                            <i class="fas fa-undo"></i>
                                            <span>Ongedaan Maken</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-product-action btn-product-action--purchase w-100"
                                                onclick="markAsPurchased(event, <?= $product['list_product_id'] ?>, <?= $list['id'] ?>)"
                                                title="Markeer als gekocht">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Ik Kocht Dit</span>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <button class="btn-product-action btn-product-action--link w-100"
                                        onclick="copyAffiliateLink('<?= $productUrl ?>')"
                                        title="Kopieer affiliate link om te delen">
                                    <i class="fas fa-share-alt"></i>
                                    <span>Link Delen</span>
                                </button>
                            </div>
                        </div>
                    </div>
                                <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (!empty($noSectionProducts)): ?>
                    <div class="section-group mb-5">
                        <div class="list-product-grid">
                            <?php foreach ($noSectionProducts as $product): ?>
                                <?php 
                                    $isClaimed = !empty($product['claimed_at']);
                                    $productUrl = base_url('index.php/out/' . $product['product_id'] . '?list=' . $list['id'] . '&lp=' . $product['list_product_id']);
                                ?>
                                <div class="list-product-card <?= $isClaimed ? 'list-product-card--claimed' : '' ?>"
                                     data-list-product-id="<?= $product['list_product_id'] ?>"
                                     data-affiliate-url="<?= esc($productUrl) ?>"
                                     role="link"
                                     tabindex="0">
                        <?php if ($isClaimed): ?>
                            <span class="claimed-badge">
                                <i class="fas fa-check-circle"></i> Gekocht
                            </span>
                        <?php endif; ?>
                        <div class="list-product-card__media">
                            <?php if ($product['image_url']): ?>
                                <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($product['title']) ?>">
                            <?php else: ?>
                                <div class="text-muted text-center">
                                    <i class="fas fa-image fa-2x"></i>
                                    <p class="small mt-2 mb-0">Geen afbeelding</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="list-product-card__body">
                            <span class="list-product-card__pill">
                                <i class="fas fa-heart text-danger"></i>
                                Favoriet
                            </span>
                            <h5 class="list-product-card__title <?= $isClaimed ? 'list-product-card__title--claimed' : '' ?>">
                                <?= esc(character_limiter($product['title'], 60)) ?>
                            </h5>
                            <p class="list-product-card__desc">
                                <?= esc(character_limiter($product['description'], 90)) ?>
                            </p>
                            <?php if ($product['price']): ?>
                                <div class="list-product-card__price">
                                    €<?= number_format($product['price'], 2) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['custom_note']): ?>
                                <div class="list-product-card__note">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    <?= esc($product['custom_note']) ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted mb-3 d-block">
                                <i class="fas fa-store me-1"></i> <?= esc($product['source']) ?>
                            </small>
                            
                            <!-- Group Gift Contribution UI -->
                            <?= view('partials/group_gift_contribution', ['product' => $product]) ?>
                            
                            <div class="list-product-card__actions d-grid gap-2">
                                <a href="<?= base_url('index.php/out/' . $product['product_id'] . '?list=' . $list['id'] . '&lp=' . $product['list_product_id']) ?>" 
                                   class="btn-product-action btn-product-action--primary w-100" 
                                   target="_blank"
                                   title="Bekijk product in winkel">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>Product Bekijken</span>
                                </a>
                                <?php if (!empty($list['is_crossable'])): ?>
                                    <?php if ($isClaimed): ?>
                                        <button class="btn-product-action btn-product-action--undo w-100"
                                                onclick="undoPurchase(event, <?= $product['list_product_id'] ?>, <?= $list['id'] ?>)"
                                                title="Maak dit item opnieuw beschikbaar">
                                            <i class="fas fa-undo"></i>
                                            <span>Ongedaan Maken</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-product-action btn-product-action--purchase w-100"
                                                onclick="markAsPurchased(event, <?= $product['list_product_id'] ?>, <?= $list['id'] ?>)"
                                                title="Markeer als gekocht">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Ik Kocht Dit</span>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <button class="btn-product-action btn-product-action--link w-100"
                                        onclick="copyAffiliateLink('<?= base_url('index.php/out/' . $product['product_id'] . '?list=' . $list['id'] . '&lp=' . $product['list_product_id']) ?>')"
                                        title="Kopieer affiliate link om te delen">
                                    <i class="fas fa-share-alt"></i>
                                    <span>Link Delen</span>
                                </button>
                            </div>
                        </div>
                    </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
    <?php endif; ?>
</div>

<?php if (empty($requiresAccess)): ?>
    <!-- Contribution Modal -->
    <?= view('partials/contribution_modal') ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
<?php if (!empty($requiresAccess)): ?>
// No additional scripts needed when access is required
<?php else: ?>
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

function switchView(viewType) {
    const container = document.getElementById('productsContainer');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    // Find all product containers (both grid and list classes)
    const productContainers = container.querySelectorAll('.list-product-grid, .list-product-list');
    
    if (viewType === 'grid') {
        productContainers.forEach(grid => {
            grid.classList.remove('list-product-list');
            grid.classList.add('list-product-grid');
        });
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('listViewPreference', 'grid');
    } else {
        productContainers.forEach(grid => {
            grid.classList.remove('list-product-grid');
            grid.classList.add('list-product-list');
        });
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('listViewPreference', 'list');
    }
}

// Restore user's view preference on page load
function markAsPurchased(evt, listProductId, listId) {
    if (!confirm('Weet je zeker dat je dit item als gekocht wilt markeren? Dit laat anderen weten dat dit cadeau al is gekocht.')) {
        return;
    }

    togglePurchaseState({
        event: evt,
        endpoint: '<?= base_url('index.php/list/claim') ?>',
        listProductId,
        listId,
        loadingLabel: 'Markeren...',
        shouldReload: true
    });
}

function undoPurchase(evt, listProductId, listId) {
    if (!confirm('Zeker weten dat je deze aankoop wilt terugdraaien? Anderen kunnen het cadeau dan weer kopen.')) {
        return;
    }

    togglePurchaseState({
        event: evt,
        endpoint: '<?= base_url('index.php/list/unclaim') ?>',
        listProductId,
        listId,
        loadingLabel: 'Herstellen...',
        shouldReload: false,
        onSuccess: (btn) => restoreProductCardState(btn, listProductId, listId)
    });
}

function togglePurchaseState({ event, endpoint, listProductId, listId, loadingLabel, shouldReload = false, onSuccess = null }) {
    const btn = event?.target?.closest('button');
    if (!btn) return;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> ${loadingLabel}`;

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            list_product_id: listProductId,
            list_id: listId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Status bijgewerkt', 'success');
            if (shouldReload) {
                location.reload();
                return;
            }
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (typeof onSuccess === 'function') {
                onSuccess(btn);
            }
        } else {
            showToast(data.message || 'Er is een fout opgetreden. Probeer het opnieuw.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Er is een fout opgetreden. Probeer het opnieuw.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function restoreProductCardState(btn, listProductId, listId) {
    const card = btn.closest('.list-product-card');
    if (card) {
        card.classList.remove('list-product-card--claimed');
        const claimedBadge = card.querySelector('.claimed-badge');
        if (claimedBadge) {
            claimedBadge.remove();
        }
        const title = card.querySelector('.list-product-card__title');
        title?.classList.remove('list-product-card__title--claimed');
    }

    const newButton = document.createElement('button');
    newButton.className = 'btn-product-action btn-product-action--purchase w-100';
    newButton.setAttribute('title', 'Markeer als gekocht');
    newButton.innerHTML = '<i class="fas fa-shopping-cart"></i><span>Ik Kocht Dit</span>';
    newButton.addEventListener('click', (ev) => markAsPurchased(ev, listProductId, listId));

    btn.replaceWith(newButton);
}

function initProductCardLinks() {
    const productCards = document.querySelectorAll('.list-product-card[role="link"]');

    productCards.forEach(card => {
        const affiliateUrl = card.dataset.affiliateUrl;
        if (!affiliateUrl) return;

        card.addEventListener('click', (e) => {
            if (shouldIgnoreCardClick(e)) return;
            openAffiliateLink(affiliateUrl, e);
        });

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                if (shouldIgnoreCardClick(e)) return;
                e.preventDefault();
                openAffiliateLink(affiliateUrl, e);
            }
        });
    });
}

function shouldIgnoreCardClick(event) {
    return Boolean(event.target.closest('button, a, input, textarea, select'));
}

function openAffiliateLink(url, event) {
    if (event.ctrlKey || event.metaKey) {
        window.open(url, '_blank');
    } else {
        window.open(url, '_blank', 'noopener');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initProductCardLinks();
});
<?php endif; ?>
</script>
<?= $this->endSection() ?>
