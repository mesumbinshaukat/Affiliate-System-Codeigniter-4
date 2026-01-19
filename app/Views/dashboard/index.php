<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Dashboard</h1>
            <p class="text-muted">Welkom terug, <?= esc($user['first_name']) ?>!</p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('index.php/dashboard/list/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nieuwe Lijst
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <h3><?= $totalLists ?></h3>
                <p class="mb-0">Totale Lijsten</p>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #3479CD, #2B63BB);">
                <h3><?= $totalClicks ?></h3>
                <p class="mb-0">Totale Klikken</p>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #3479CD, #4F9BFF);">
                <h3>€<?= number_format($salesStats['total_commission'] ?? 0, 2) ?></h3>
                <p class="mb-0">Commissies</p>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #2957A8, #3479CD);">
                <h3>€<?= number_format($contributionStats['total_amount'] ?? 0, 2) ?></h3>
                <p class="mb-0">Groepscadeaus (<?= $contributionStats['count'] ?? 0 ?>)</p>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <a href="<?= base_url('index.php/dashboard/lists') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-list fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Mijn Lijsten</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="<?= base_url('index.php/dashboard/list/create') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-plus-circle fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Lijst Maken</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="<?= base_url('index.php/drawings') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-dice fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Loten Trekken</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="<?= base_url('index.php/dashboard/analytics') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Analytica</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Second Row - New Tab -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <a href="<?= base_url('index.php/dashboard/purchased') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Gekochte Producten</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="<?= base_url('index.php/dashboard/invitations') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-envelope fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Lijstuitnodigingen</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Lists -->
    <h3 class="mb-3">Uw Recente Lijsten</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Categorie</th>
                    <th>Status</th>
                    <th>Producten</th>
                    <th>Weergaven</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lists)): ?>
                    <?php foreach (array_slice($lists, 0, 5) as $list): ?>
                        <tr>
                            <td>
                                <strong><?= esc($list['title']) ?></strong>
                            </td>
                            <td><?= esc($list['category_name'] ?? 'Zonder categorie') ?></td>
                            <td>
                                <?php if ($list['status'] === 'published'): ?>
                                    <span class="badge bg-success">Gepubliceerd</span>
                                <?php elseif ($list['status'] === 'draft'): ?>
                                    <span class="badge bg-warning">Concept</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Privé</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $list['product_count'] ?? 0 ?></td>
                            <td><?= number_format($list['views']) ?></td>
                            <td>
                                <a href="<?= base_url('index.php/dashboard/list/edit/' . $list['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($list['status'] === 'published'): ?>
                                    <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Nog geen lijsten. <a href="<?= base_url('index.php/dashboard/list/create') ?>">Maak uw eerste lijst</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
