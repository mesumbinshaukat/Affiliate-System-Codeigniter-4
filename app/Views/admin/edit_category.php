<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Categorie Bewerken: <?= esc($category['name']) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('index.php/admin/category/edit/' . $category['id']) ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Categorienaam *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= esc($category['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Beschrijving</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= esc($category['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Pictogram (Font Awesome klassenaam)</label>
                            <input type="text" class="form-control" id="icon" name="icon" value="<?= esc($category['icon']) ?>" placeholder="bijv. laptop, home, shirt">
                            <small class="text-muted">Bezoek <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a> voor pictogramnamen</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $category['status'] === 'active' ? 'selected' : '' ?>>Actief</option>
                                <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : '' ?>>Inactief</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Categorie Bijwerken</button>
                            <a href="<?= base_url('index.php/admin/categories') ?>" class="btn btn-secondary">Annuleren</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
