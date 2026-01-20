<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1>Nieuwe Lijst Maken</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card">
                <div class="card-body">
                    <form method="post" action="<?= base_url('index.php/dashboard/list/create') ?>">
                        <div class="mb-3">
                            <label for="title" class="form-label">Lijsttitel *</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Categorie</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Selecteer een categorie</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Beschrijving</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?= old('description') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_crossable" name="is_crossable" value="1" <?= old('is_crossable', '1') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_crossable">
                                    <strong>Sta toe dat items als gekocht gemarkeerd worden</strong>
                                    <small class="d-block text-muted">Bezoekers kunnen items afvinken nadat ze deze hebben gekocht (handig voor verlanglijstjes)</small>
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="fas fa-bell text-primary"></i> Evenement & Herinneringen</h5>
                        <p class="text-muted small mb-3">Stel een evenementdatum in (bijv. verjaardag) en ontvang automatische herinneringen</p>

                        <div class="mb-3">
                            <label for="event_date" class="form-label">Evenementdatum (optioneel)</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" value="<?= old('event_date') ?>">
                            <small class="text-muted">Bijvoorbeeld uw verjaardag: 19 februari 2025</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="reminder_enabled" name="reminder_enabled" value="1" <?= old('reminder_enabled') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="reminder_enabled">
                                    <strong>Stuur automatische e-mail herinneringen</strong>
                                    <small class="d-block text-muted">Medewerkers ontvangen herinneringen voor het evenement</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="reminder_settings" style="display: none;">
                            <label for="reminder_intervals" class="form-label">Herinneringsmomenten (in dagen)</label>
                            <input type="text" class="form-control" id="reminder_intervals" name="reminder_intervals" value="<?= old('reminder_intervals', '30,14,7') ?>" placeholder="30,14,7">
                            <small class="text-muted">Kommagescheiden: bijv. "30,14,7" stuurt herinneringen 30, 14 en 7 dagen voor het evenement</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Lijst Maken</button>
                            <a href="<?= base_url('index.php/dashboard/lists') ?>" class="btn btn-secondary">Annuleren</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card">
                <div class="card-body">
                    <h5 class="card-title">Tips</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Kies een beschrijvende titel
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Voeg een gedetailleerde beschrijving toe
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Selecteer de juiste categorie
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            Voeg producten toe na het maken van uw lijst
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reminderEnabled = document.getElementById('reminder_enabled');
    const reminderSettings = document.getElementById('reminder_settings');
    const eventDate = document.getElementById('event_date');
    
    function toggleReminderSettings() {
        if (reminderEnabled && reminderEnabled.checked && eventDate.value) {
            reminderSettings.style.display = 'block';
        } else {
            reminderSettings.style.display = 'none';
        }
    }
    
    if (reminderEnabled) reminderEnabled.addEventListener('change', toggleReminderSettings);
    if (eventDate) eventDate.addEventListener('change', toggleReminderSettings);
    
    // Initial state
    toggleReminderSettings();
});
</script>

<?= $this->endSection() ?>
