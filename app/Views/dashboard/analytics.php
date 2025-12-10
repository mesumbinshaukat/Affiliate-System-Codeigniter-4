<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Analytica</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stats-card">
                <h3><?= $totalClicks ?></h3>
                <p class="mb-0">Totale Klikken</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Recente Klikken</h5>
            <div class="table-responsive">
                <table class="table table-hover">
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
                                    <td><?= date('M d, Y H:i', strtotime($click['created_at'])) ?></td>
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
