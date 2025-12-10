<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Admin Dashboard</h1>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?= $totalUsers ?></h3>
                <p class="mb-0">Total Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                <h3><?= $totalLists ?></h3>
                <p class="mb-0">Total Lists</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <h3><?= $totalProducts ?></h3>
                <p class="mb-0">Total Products</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <h3><?= $totalClicks ?></h3>
                <p class="mb-0">Total Clicks</p>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="<?= base_url('index.php/admin/users') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h5>Manage Users</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= base_url('index.php/admin/lists') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-list fa-3x text-success mb-3"></i>
                    <h5>Manage Lists</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= base_url('index.php/admin/categories') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-folder fa-3x text-warning mb-3"></i>
                    <h5>Categories</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= base_url('index.php/admin/analytics') ?>" class="card text-center text-decoration-none">
                <div class="card-body">
                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                    <h5>Analytics</h5>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Users</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                    <tr>
                                        <td><?= esc($user['username']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>"><?= esc($user['role']) ?></span></td>
                                        <td><span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>"><?= esc($user['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Lists -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Lists</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLists as $list): ?>
                                    <tr>
                                        <td><?= esc(character_limiter($list['title'], 30)) ?></td>
                                        <td><?= esc($list['username']) ?></td>
                                        <td><span class="badge bg-<?= $list['status'] === 'published' ? 'success' : 'warning' ?>"><?= esc($list['status']) ?></span></td>
                                        <td><?= number_format($list['views']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
