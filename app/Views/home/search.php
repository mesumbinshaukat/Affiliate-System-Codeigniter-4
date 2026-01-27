<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Lijsten Zoeken</h1>

    <form method="get" action="<?= base_url('index.php/search') ?>" class="mb-4">
        <div class="input-group input-group-lg flex-column flex-md-row">
            <input type="text" name="q" class="form-control mb-3 mb-md-0" placeholder="Zoeken naar lijsten..." value="<?= esc($query ?? '') ?>">
            <button class="btn btn-primary w-100 w-md-auto" type="submit">
                <i class="fas fa-search"></i> Zoeken
            </button>
        </div>
    </form>

    <?php if (isset($query) && $query): ?>
        <h3 class="mb-3">Resultaten voor "<?= esc($query) ?>"</h3>

        <div class="row">
            <?php if (!empty($lists)): ?>
                <?php foreach ($lists as $list): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="card list-card h-100 text-decoration-none text-dark">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2"><?= esc($list['title']) ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= esc(character_limiter($list['description'], 100)) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> <?= esc($list['username'] ?? 'Onbekend') ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye"></i> <?= number_format($list['views']) ?>
                                    </small>
                                </div>
                            </div>
                        </a>
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
