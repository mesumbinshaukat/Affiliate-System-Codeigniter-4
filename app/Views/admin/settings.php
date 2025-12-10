<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Site-instellingen</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('index.php/admin/settings') ?>">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Sitenaam</label>
                            <input type="text" class="form-control" id="site_name" name="settings[site_name]" value="<?= esc($settings['site_name'] ?? 'Lijstje.nl') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Sitebeschrijving</label>
                            <textarea class="form-control" id="site_description" name="settings[site_description]" rows="3"><?= esc($settings['site_description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="items_per_page" class="form-label">Items Per Pagina</label>
                            <input type="number" class="form-control" id="items_per_page" name="settings[items_per_page]" value="<?= esc($settings['items_per_page'] ?? 12) ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_registration" name="settings[enable_registration]" value="1" <?= ($settings['enable_registration'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_registration">
                                    Gebruikersregistratie inschakelen
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Instellingen Opslaan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
