<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">🎁 Gekochte Producten</h1>
    <p class="text-muted">Overzicht van alle producten die als gekocht zijn gemarkeerd op uw lijsten</p>

    <!-- Stats -->
    <div class="stats-grid mb-4">
        <div class="stats-card">
            <h3><?= $totalPurchased ?></h3>
            <p>Totaal Gekocht</p>
        </div>
        <div class="stats-card" style="background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #10b981, #059669);">
            <h3><?= count($listCounts) ?></h3>
            <p>Lijsten met Aankopen</p>
        </div>
        <div class="stats-card" style="background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <h3><?= $totalPurchased > 0 ? number_format(($totalPurchased / count($listCounts)), 1) : 0 ?></h3>
            <p>Gem. per Lijst</p>
        </div>
    </div>

    <!-- Breakdown by List -->
    <?php if (!empty($listCounts)): ?>
    <div class="glass-card mb-4">
        <div class="card-body">
            <h5 class="card-title">Aankopen per Lijst</h5>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Lijst</th>
                            <th class="text-end">Gekocht</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listCounts as $listId => $data): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($data['list_title']) ?></strong>
                                </td>
                                <td class="text-end">
                                    <span class="badge-soft success"><?= $data['count'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Purchased Products Table -->
    <div class="glass-card">
        <div class="card-body">
            <h5 class="card-title">Alle Gekochte Producten</h5>
            <?php if (!empty($purchasedProducts)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Afbeelding</th>
                                <th>Product</th>
                                <th>Lijst</th>
                                <th>Bron</th>
                                <th>Prijs</th>
                                <th>Gekocht op</th>
                                <th>Tracking ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchasedProducts as $product): ?>
                                <tr>
                                    <td>
                                        <?php if ($product['image_url']): ?>
                                            <img src="<?= esc($product['image_url']) ?>" 
                                                 alt="<?= esc($product['product_title']) ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($product['product_title']) ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('index.php/list/' . $product['list_slug']) ?>" 
                                           target="_blank"
                                           class="text-decoration-none">
                                            <?= esc($product['list_title']) ?>
                                            <i class="fas fa-external-link-alt fa-xs ms-1"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($product['source']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($product['price']): ?>
                                            <strong>€<?= number_format($product['price'], 2) ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= date('d-m-Y H:i', strtotime($product['claimed_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($product['claimed_by_subid']): ?>
                                            <?php if (strpos($product['claimed_by_subid'], 'manual_') === 0): ?>
                                                <span class="badge-soft warning">
                                                    <i class="fas fa-hand-pointer"></i> Handmatig
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-soft info" title="<?= esc($product['claimed_by_subid']) ?>">
                                                    <i class="fas fa-link"></i> Affiliate
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                    <h5>Nog geen gekochte producten</h5>
                    <p class="mb-0">Wanneer bezoekers items op uw lijsten als gekocht markeren, verschijnen ze hier.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info mt-4">
        <h6 class="alert-heading">
            <i class="fas fa-info-circle"></i> Over Gekochte Producten
        </h6>
        <p class="mb-0">
            Deze pagina toont alle producten die als gekocht zijn gemarkeerd op uw lijsten. 
            Dit kan automatisch gebeuren via Bol.com affiliate tracking of handmatig door bezoekers via de "Ik Kocht Dit" knop.
            <strong>Let op:</strong> U ziet niet wie het product heeft gekocht (privacy-vriendelijk).
        </p>
    </div>
</div>

<?= $this->endSection() ?>
