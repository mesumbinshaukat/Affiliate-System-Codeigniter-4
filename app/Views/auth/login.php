<?= $this->extend('layouts/main') ?>


<?= $this->section('content') ?>



<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Inloggen</h2>
                    
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Login Buttons -->
                    <div class="mb-4">
                        <a href="<?= base_url('index.php/auth/social/facebook') ?>" class="btn btn-primary w-100 mb-2" style="background-color: #1877f2; border-color: #1877f2;">
                            <i class="fab fa-facebook-f me-2"></i> Inloggen met Facebook
                        </a>
                        <a href="<?= base_url('index.php/auth/social/google') ?>" class="btn btn-danger w-100 mb-2">
                            <i class="fab fa-google me-2"></i> Inloggen met Google
                        </a>
                    </div>
                    
                    <div class="text-center mb-3">
                        <span class="text-muted">of</span>
                    </div>
                    
                    <form method="post" action="<?= base_url('index.php/login') ?>" id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required autofocus>
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" id="password" name="password" required>
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100" id="loginBtn">Inloggen</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Geen account? <a href="<?= base_url('index.php/register') ?>">Registreer hier</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('loginBtn');
    
    // Disable button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Inloggen...';
});
</script>
<?= $this->endSection() ?>
