<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Analytics</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Products</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Clicks</th>
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
                    <h5 class="card-title">Top Lists</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>List</th>
                                    <th>Author</th>
                                    <th>Clicks</th>
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

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Click Statistics</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Clicks</th>
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
