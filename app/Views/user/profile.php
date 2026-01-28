<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user"></i> Mijn Profiel</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('user/updateProfile') ?>">
                        <!-- Name Fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Voornaam <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                                       name="first_name" value="<?= old('first_name', esc($user['first_name'])) ?>" required>
                                <?php if (session('errors.first_name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Achternaam <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                                       name="last_name" value="<?= old('last_name', esc($user['last_name'])) ?>" required>
                                <?php if (session('errors.last_name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Email (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">E-mailadres</label>
                            <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                            <small class="text-muted">E-mailadres kan niet worden gewijzigd</small>
                        </div>

                        <!-- Username (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gebruikersnaam</label>
                            <input type="text" class="form-control" value="<?= esc($user['username']) ?>" disabled>
                            <small class="text-muted">Gebruikersnaam kan niet worden gewijzigd</small>
                        </div>

                        <!-- Date of Birth -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Geboortedatum <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= session('errors.date_of_birth') ? 'is-invalid' : '' ?>" 
                                       name="date_of_birth" value="<?= old('date_of_birth', esc($user['date_of_birth'])) ?>" required>
                                <?php if (session('errors.date_of_birth')): ?>
                                    <div class="invalid-feedback"><?= session('errors.date_of_birth') ?></div>
                                <?php endif; ?>
                                <?php if ($age !== null): ?>
                                    <small class="text-muted">Leeftijd: <?= $age ?> jaar</small>
                                <?php endif; ?>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Geslacht (optioneel)</label>
                                <select class="form-select <?= session('errors.gender') ? 'is-invalid' : '' ?>" name="gender">
                                    <option value="">-- Selecteer --</option>
                                    <option value="male" <?= old('gender', $user['gender']) === 'male' ? 'selected' : '' ?>>Man</option>
                                    <option value="female" <?= old('gender', $user['gender']) === 'female' ? 'selected' : '' ?>>Vrouw</option>
                                    <option value="other" <?= old('gender', $user['gender']) === 'other' ? 'selected' : '' ?>>Anders</option>
                                </select>
                                <?php if (session('errors.gender')): ?>
                                    <div class="invalid-feedback"><?= session('errors.gender') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bio (optioneel)</label>
                            <textarea class="form-control <?= session('errors.bio') ? 'is-invalid' : '' ?>" 
                                      name="bio" rows="4" maxlength="500" placeholder="Vertel iets over jezelf..."><?= old('bio', esc($user['bio'] ?? '')) ?></textarea>
                            <small class="text-muted">Maximaal 500 tekens</small>
                            <?php if (session('errors.bio')): ?>
                                <div class="invalid-feedback"><?= session('errors.bio') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Account Info -->
                        <div class="alert alert-info">
                            <strong>Account informatie:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Lid sinds: <?= date('d-m-Y', strtotime($user['created_at'])) ?></li>
                                <li>Status: <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($user['status']) ?></span></li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Profiel Opslaan
                            </button>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Terug naar Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
