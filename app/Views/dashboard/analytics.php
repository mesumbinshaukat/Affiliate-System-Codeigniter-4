<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Analytica</h1>

    <!-- Sales Overview Section -->
    <div class="stats-grid mb-4">
        <div class="stats-card">
            <h3><?= $totalClicks ?></h3>
            <p>Totale Klikken</p>
        </div>
        <div class="stats-card" style="background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #10b981, #059669);">
            <h3><?= $salesStats['total_sales'] ?? 0 ?></h3>
            <p>Totale Verkopen</p>
        </div>
        <div class="stats-card" style="background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #f59e0b, #d97706);">
            <h3><?= $salesStats['approved_sales'] ?? 0 ?></h3>
            <p>Goedgekeurde Bestellingen</p>
        </div>
    </div>

    <!-- Sales Status Breakdown -->
    <?php if (($salesStats['total_sales'] ?? 0) > 0): ?>
    <div class="glass-card mb-4">
        <div class="card-body">
            <h5 class="card-title">Verkoopstatus Overzicht</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-success"><?= $salesStats['approved_sales'] ?? 0 ?></h4>
                        <p class="text-muted mb-0">Goedgekeurd</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-warning"><?= $salesStats['pending_sales'] ?? 0 ?></h4>
                        <p class="text-muted mb-0">In Behandeling</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-danger"><?= $salesStats['rejected_sales'] ?? 0 ?></h4>
                        <p class="text-muted mb-0">Afgewezen</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sales Table -->
    <div class="glass-card mb-4 p-3">
        <div class="card-body">
            <h5 class="card-title">Uw Verkopen</h5>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Bedrag</th>
                            <th>Status</th>
                            <th>Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sales)): ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td><code><?= esc($sale['order_id']) ?></code></td>
                                    <td><strong>€<?= number_format($sale['commission'], 2, ',', '') ?></strong></td>
                                    <td>
                                        <span class="badge-soft <?= 
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
                                    <td><?= date('d-m-Y H:i', strtotime($sale['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Nog geen verkopen geregistreerd</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Clicks Table -->
    <div class="glass-card p-3">
        <div class="card-body">
            <h5 class="card-title">Recente Klikken</h5>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Lijst</th>
                            <th>Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clicks)): ?>
                            <?php foreach ($clicks as $click): ?>
                                <tr>
                                    <td><?= esc($click['product_title']) ?></td>
                                    <td><?= esc($click['list_title'] ?? 'N/A') ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($click['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Nog geen klikken</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
