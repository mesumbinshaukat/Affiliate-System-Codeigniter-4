<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="dashboard-hero">
        <div class="hero-meta">
            <h1>Welkom terug, <?= esc($user['first_name']) ?>!</h1>
            <p>Overzicht van uw lijsten, verkoopcijfers en recente activiteiten.</p>
            <div class="hero-actions">
                <a href="<?= base_url('dashboard/list/create') ?>" class="hero-btn hero-btn-primary">
                    <span class="hero-btn-icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <div>
                        <strong>Nieuwe Lijst</strong>
                        <small>Start direct en deel hem meteen</small>
                    </div>
                </a>
                <a href="<?= base_url('dashboard/analytics') ?>" class="hero-btn hero-btn-ghost">
                    <span class="hero-btn-icon">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <div>
                        <strong>Bekijk Analytics</strong>
                        <small>Ontdek trends en prestaties</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid mb-4">
        <div class="stats-card">
            <h3><?= $totalLists ?></h3>
            <p>Totale Lijsten</p>
        </div>
        <div class="stats-card" style="background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #3479CD, #2B63BB);">
            <h3><?= $totalClicks ?></h3>
            <p>Totale Klikken</p>
        </div>
        <div class="stats-card" style="background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #2957A8, #3479CD);">
            <h3>€<?= number_format($contributionStats['total_amount'] ?? 0, 2, ',', '') ?></h3>
            <p>Groepscadeaus (<?= $contributionStats['count'] ?? 0 ?>)</p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <a href="<?= base_url('dashboard/lists') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-list fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Mijn Lijsten</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="<?= base_url('dashboard/list/create') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-plus-circle fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Lijst Maken</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="<?= base_url('drawings') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-dice fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Loten Trekken</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="<?= base_url('dashboard/analytics') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-chart-line fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Analytica</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-6">
            <a href="<?= base_url('dashboard/purchased') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Gekochte Producten</h5>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-6">
            <a href="<?= base_url('dashboard/invitations') ?>" class="card text-center text-decoration-none h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-envelope fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Lijstuitnodigingen</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Lists -->
    <h3 class="mb-3">Uw Recente Lijsten</h3>
    <div class="glass-card p-3 mb-4">
        <div class="table-responsive">
            <table class="data-table">
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
                                        <span class="badge-soft success">Gepubliceerd</span>
                                    <?php elseif ($list['status'] === 'draft'): ?>
                                        <span class="badge-soft warning">Concept</span>
                                    <?php else: ?>
                                        <span class="badge-soft muted">Privé</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $list['product_count'] ?? 0 ?></td>
                                <td><?= number_format($list['views']) ?></td>
                                <td>
                                    <a href="<?= base_url('dashboard/list/edit/' . $list['id']) ?>" class="btn btn-sm btn-outline-primary" title="Bewerken">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($list['status'] === 'published'): ?>
                                        <a href="<?= base_url('list/' . $list['slug']) ?>" class="btn btn-sm btn-outline-info ms-1" target="_blank" title="Bekijken">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Nog geen lijsten. <a href="<?= base_url('dashboard/list/create') ?>">Maak uw eerste lijst</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
