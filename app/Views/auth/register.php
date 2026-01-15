<?= $this->extend('layouts/landing') ?>

<?= $this->section('styles') ?>
<style>
    .auth-hero {
        padding: 3.5rem 0 2rem;
        text-align: center;
    }

    .auth-section {
        padding: 0 0 4rem;
    }

    .auth-card {
        background: #fff;
        border-radius: 28px;
        box-shadow: 0 25px 60px rgba(7, 24, 94, 0.15);
        padding: 2.5rem;
    }

    .auth-card h2 {
        font-weight: 700;
        color: #071146;
    }

    .auth-card p.lead {
        color: #5A6EA7;
    }

    .social-button {
        border-radius: 999px;
        padding: 0.95rem 1.5rem;
        font-weight: 600;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        width: 100%;
    }

    .social-button.facebook {
        background: #1877f2;
        color: #fff;
    }

    .social-button.google {
        background: #EA4335;
        color: #fff;
    }

    .auth-divider {
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #7B8AAC;
        letter-spacing: 0.08em;
    }

    .auth-form .form-label {
        font-weight: 600;
        color: #0b1f55;
    }

    .form-section-title {
        font-weight: 700;
        color: #071146;
        margin-bottom: 1rem;
    }

    .auth-submit {
        border-radius: 999px;
        padding: 0.95rem 1.5rem;
        font-weight: 600;
        background: #2F4CF1;
        border: none;
        color: #fff;
        width: 100%;
    }

    .privacy-note {
        font-size: 0.9rem;
        color: #7B8AAC;
    }

    .privacy-note a {
        color: #2F4CF1;
        text-decoration: none;
    }

    .privacy-note a:hover {
        text-decoration: underline;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="auth-hero">
    <div class="container">
        <span class="badge text-bg-light text-primary mb-3 px-3 py-2 rounded-pill">Nieuw bij Remcom</span>
        <h1 class="hero-heading">Maak een account aan en start met delen.</h1>
        <p class="hero-text mx-auto">Organiseer lootjes, beheer verlanglijstjes en houd iedereen blij vanuit één platform.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="auth-card">
                    <div class="mb-4 text-center">
                        <h2>Account aanmaken</h2>
                        <p class="lead">Meld je aan en begin vandaag nog met delen.</p>
                    </div>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <a href="<?= base_url('index.php/auth/social/facebook') ?>" class="social-button facebook w-100 mb-3">
                            <i class="fab fa-facebook-f me-2"></i> Registreren met Facebook
                        </a>
                        <a href="<?= base_url('index.php/auth/social/google') ?>" class="social-button google w-100">
                            <i class="fab fa-google me-2"></i> Registreren met Google
                        </a>
                    </div>

                    <div class="text-center my-3 auth-divider">of registreer met e-mail</div>

                    <form method="post" action="<?= base_url('index.php/register') ?>" id="registerForm" class="auth-form">
                        <div class="form-section-title">Persoonlijke gegevens</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Voornaam<span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= old('first_name') ?>" required placeholder="Voornaam">
                                <?php if (session('errors.first_name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Achternaam<span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= old('last_name') ?>" required placeholder="Achternaam">
                                <?php if (session('errors.last_name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Geboortedatum<span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= session('errors.date_of_birth') ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" value="<?= old('date_of_birth') ?>" required>
                                <?php if (session('errors.date_of_birth')): ?>
                                    <div class="invalid-feedback"><?= session('errors.date_of_birth') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Geslacht (optioneel)</label>
                                <select class="form-select <?= session('errors.gender') ? 'is-invalid' : '' ?>" id="gender" name="gender">
                                    <option value="">Selecteer</option>
                                    <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Man</option>
                                    <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Vrouw</option>
                                    <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Anders</option>
                                </select>
                                <?php if (session('errors.gender')): ?>
                                    <div class="invalid-feedback"><?= session('errors.gender') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">E-mailadres<span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required placeholder="Uw e-mailadres">
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-section-title">Wachtwoord</div>
                        <div class="mb-4">
                            <input type="password" class="form-control mb-3 <?= session('errors.password') ? 'is-invalid' : '' ?>" id="password" name="password" required minlength="8" placeholder="Wachtwoord">
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                            <input type="password" class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>" id="password_confirm" name="password_confirm" required minlength="8" placeholder="Wachtwoord bevestigen">
                            <?php if (session('errors.password_confirm')): ?>
                                <div class="invalid-feedback"><?= session('errors.password_confirm') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Houd mij op de hoogte van mijn lijsten per e-mail
                                </label>
                            </div>
                            <p class="privacy-note mt-2">Uiteraard gaan wij voorzichtig om met deze informatie. Lees onze <a href="#">privacyverklaring</a>.</p>
                        </div>

                        <button type="submit" class="auth-submit" id="submitBtn">
                            Account aanmaken <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const submitBtn = document.getElementById('submitBtn');

    document.querySelectorAll('.custom-error').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    let hasError = false;

    if (password !== passwordConfirm) {
        e.preventDefault();
        const confirmInput = document.getElementById('password_confirm');
        confirmInput.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback custom-error';
        errorDiv.textContent = 'Wachtwoorden komen niet overeen';
        confirmInput.parentNode.appendChild(errorDiv);
        hasError = true;
    }

    if (password.length < 8) {
        e.preventDefault();
        const passwordInput = document.getElementById('password');
        passwordInput.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback custom-error';
        errorDiv.textContent = 'Wachtwoord moet minimaal 8 tekens zijn';
        passwordInput.parentNode.appendChild(errorDiv);
        hasError = true;
    }

    if (!hasError) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Account aanmaken...';
    }
});
</script>
<?= $this->endSection() ?>
