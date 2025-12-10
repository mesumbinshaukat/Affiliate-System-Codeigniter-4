<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Lijsten Zoeken</h1>

    <form method="get" action="<?= base_url('index.php/search') ?>" class="mb-4">
        <div class="input-group input-group-lg">
            <input type="text" name="q" class="form-control" placeholder="Zoeken naar lijsten..." value="<?= esc($query ?? '') ?>">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i> Zoeken
            </button>
        </div>
    </form>

    <?php if (isset($query) && $query): ?>
        <h3 class="mb-3">Resultaten voor "<?= esc($query) ?>"</h3>

        <div class="row">
            <?php if (!empty($lists)): ?>
                <?php foreach ($lists as $list): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card list-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="text-decoration-none text-dark">
                                        <?= esc($list['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted"><?= esc(character_limiter($list['description'], 100)) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> <?= esc($list['username'] ?? 'Onbekend') ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye"></i> <?= number_format($list['views']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        Geen lijsten gevonden die aan uw zoekopdracht voldoen.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($pager)): ?>
            <div class="mt-4">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
