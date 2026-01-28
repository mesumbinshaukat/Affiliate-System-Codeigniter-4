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

    .social-button {
        border-radius: 999px;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        width: 100%;
    }

    .social-button.google {
        background: #EA4335;
        color: #fff;
    }

    .social-button.facebook {
        background: #1877f2;
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

    .auth-submit {
        border-radius: 999px;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        background: #3479CD;
        border: none;
        color: #fff;
        width: 100%;
    }

    .auth-link {
        color: #3479CD;
        font-weight: 600;
        text-decoration: none;
    }

    .auth-link:hover {
        text-decoration: underline;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="auth-hero">
    <div class="container">
        <span class="badge text-bg-light text-primary mb-3 px-3 py-2 rounded-pill">Welkom terug bij Remcom</span>
        <h1 class="hero-heading">Log In en beheer je verlanglijstjes.</h1>
        <p class="hero-text mx-auto">Houd overzicht over lootjes, cadeaus en gedeelde lijstjes vanuit één vertrouwde plek.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger"><?= session('error') ?></div>
                    <?php endif; ?>
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
                        <a href="<?= base_url('auth/social/facebook') ?>" class="social-button facebook w-100 mb-3">
                            <i class="fab fa-facebook-f me-2"></i> Inloggen met Facebook
                        </a>
                        <a href="<?= base_url('auth/social/google') ?>" class="social-button google w-100">
                            <i class="fab fa-google me-2"></i> Inloggen met Google
                        </a>
                    </div>

                    <div class="text-center my-3 auth-divider">of</div>

                    <form method="post" action="<?= base_url('login') ?>" id="loginForm" class="auth-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required autofocus>
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" id="password" name="password" required>
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="auth-submit" id="loginBtn">Inloggen</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Nog geen account? <a class="auth-link" href="<?= base_url('register') ?>">Registreer hier</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('loginForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('loginBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Inloggen...';
});
</script>
<?= $this->endSection() ?>
