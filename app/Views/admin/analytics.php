<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Analytica</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Populairste Producten</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Klikken</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProducts as $product): ?>
                                    <tr>
                                        <td><?= esc(character_limiter($product['title'], 40)) ?></td>
                                        <td><span class="badge bg-primary"><?= $product['click_count'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Populairste Lijsten</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Lijst</th>
                                    <th>Auteur</th>
                                    <th>Klikken</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topLists as $list): ?>
                                    <tr>
                                        <td><?= esc(character_limiter($list['title'], 30)) ?></td>
                                        <td><?= esc($list['username']) ?></td>
                                        <td><span class="badge bg-success"><?= $list['click_count'] ?? 0 ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales & Commissions Overview -->
    <div class="row mb-4 mt-4">
        <div class="col-md-12">
            <h3><i class="fas fa-chart-line"></i> Verkopen & Commissies Overzicht</h3>
        </div>
    </div>

    <!-- Sales Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary"><?= $salesStats['total_sales'] ?? 0 ?></h3>
                    <p class="text-muted mb-0">Totale Verkopen</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success"><?= $salesStats['approved_sales'] ?? 0 ?></h3>
                    <p class="text-muted mb-0">Goedgekeurd</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning"><?= $salesStats['pending_sales'] ?? 0 ?></h3>
                    <p class="text-muted mb-0">In Behandeling</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">€<?= number_format($salesStats['total_commission'] ?? 0, 2) ?></h3>
                    <p class="text-muted mb-0">Totale Commissie</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Users by Commission -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Top Gebruikers op Commissie</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($salesByUser)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Gebruiker</th>
                                <th>Totale Verkopen</th>
                                <th>Goedgekeurd</th>
                                <th>Commissie</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesByUser as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                        <br>
                                        <small class="text-muted">@<?= esc($user['username']) ?></small>
                                    </td>
                                    <td><?= $user['total_sales'] ?? 0 ?></td>
                                    <td><?= $user['approved_sales'] ?? 0 ?></td>
                                    <td><strong>€<?= number_format($user['total_commission'] ?? 0, 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Geen verkoopgegevens beschikbaar.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Complete Sales History -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Volledige Verkoopgeschiedenis</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($allSales)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Gebruiker</th>
                                <th>Product ID</th>
                                <th>Hoeveelheid</th>
                                <th>Commissie</th>
                                <th>Status</th>
                                <th>Datum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allSales as $sale): ?>
                                <tr>
                                    <td><code><?= esc($sale['order_id']) ?></code></td>
                                    <td>
                                        <small>
                                            <?= esc($sale['first_name'] . ' ' . $sale['last_name']) ?>
                                            <br>
                                            @<?= esc($sale['username']) ?>
                                        </small>
                                    </td>
                                    <td><?= esc($sale['product_id'] ?? 'N/A') ?></td>
                                    <td><?= $sale['quantity'] ?? 1 ?></td>
                                    <td>€<?= number_format($sale['commission'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $sale['status'] === 'approved' ? 'success' : 
                                            ($sale['status'] === 'pending' ? 'warning' : 'danger')
                                        ?>">
                                            <?php if ($sale['status'] === 'approved'): ?>
                                                Goedgekeurd
                                            <?php elseif ($sale['status'] === 'pending'): ?>
                                                In Behandeling
                                            <?php else: ?>
                                                Afgewezen
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><small><?= date('d-m-Y H:i', strtotime($sale['created_at'])) ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Geen verkoopgegevens beschikbaar.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Klikstatistieken</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Klikken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($clickStats, 0, 10) as $stat): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($stat['date'])) ?></td>
                                <td><?= number_format($stat['count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
