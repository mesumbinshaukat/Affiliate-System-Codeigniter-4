<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h1 class="mb-4"><i class="fas fa-dice"></i> Nieuwe Loting Aanmaken</h1>

            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('index.php/drawings/create') ?>">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titel *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title') ?>" required 
                                   placeholder="bijv. Kerstmis 2025, Sinterklaas, Verjaardagsfeest">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Beschrijving</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                      placeholder="Voeg details toe over de loting..."><?= old('description') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="event_date" class="form-label">Datum van het evenement</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" 
                                   value="<?= old('event_date') ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Loting Aanmaken
                            </button>
                            <a href="<?= base_url('index.php/drawings') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Terug
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-lightbulb"></i> Hoe het werkt:</h5>
                <ol class="mb-0">
                    <li>Maak een nieuwe loting aan met een titel en datum</li>
                    <li>Voeg deelnemers toe door hun gebruikersnaam in te voeren</li>
                    <li>Klik op "Trek Loten" om willekeurig toe te wijzen wie voor wie cadeaus koopt</li>
                    <li>Deelnemers kunnen dan de verlanglijst van hun toegewezen persoon bekijken</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
