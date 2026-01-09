<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <div class="sidebar-menu">
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('index.php/register') ?>">Maak een lijst</a></li>
                    <li><a href="#">Vind een lijst</a></li>
                    <li class="text-danger"><i class="fas fa-gift"></i> Loten trekken</li>
                    <li><a href="#">Winkels</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="mb-4">Account aanmaken</h2>
                    <p class="text-muted mb-4">Meld u aan om uw lijsten te maken</p>
                    
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Registration Buttons -->
                    <div class="mb-4">
                        <a href="<?= base_url('auth/social/facebook') ?>" class="btn btn-primary w-100 mb-2" style="background-color: #1877f2; border-color: #1877f2;">
                            <i class="fab fa-facebook-f me-2"></i> Registreren met Facebook
                        </a>
                        <a href="<?= base_url('auth/social/google') ?>" class="btn btn-danger w-100 mb-2">
                            <i class="fab fa-google me-2"></i> Registreren met Google
                        </a>
                    </div>
                    
                    <div class="text-center mb-3">
                        <span class="text-muted">of registreer met e-mail</span>
                    </div>
                    
                    <form method="post" action="<?= base_url('index.php/register') ?>" id="registerForm">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Voornaam<span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= old('first_name') ?>" required placeholder="Voornaam">
                                    <?php if (session('errors.first_name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= old('last_name') ?>" required placeholder="Achternaam">
                                    <?php if (session('errors.last_name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="date" class="form-control <?= session('errors.date_of_birth') ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" value="<?= old('date_of_birth') ?>" required>
                                    <small class="text-muted">Geboortedatum</small>
                                    <?php if (session('errors.date_of_birth')): ?>
                                        <div class="invalid-feedback"><?= session('errors.date_of_birth') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <select class="form-select <?= session('errors.gender') ? 'is-invalid' : '' ?>" id="gender" name="gender">
                                        <option value="">Geslacht (optioneel)</option>
                                        <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Man</option>
                                        <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Vrouw</option>
                                        <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Anders</option>
                                    </select>
                                    <?php if (session('errors.gender')): ?>
                                        <div class="invalid-feedback"><?= session('errors.gender') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required placeholder="Uw e-mailadres">
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Wachtwoord</label>
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
                                    Houd mij op de hoogte van mijn lijsten per e-mail<br>
                                    <small class="text-muted">Uiteraard gaan wij voorzichtig om met deze informatie. Lees onze <a href="#">privacyverklaring</a>.</small>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            Account aanmaken <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const submitBtn = document.getElementById('submitBtn');
    
    // Clear previous custom errors
    document.querySelectorAll('.custom-error').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    let hasError = false;
    
    // Check password match
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
    
    // Check password length
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
    
    // Disable button to prevent double submission
    if (!hasError) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Account aanmaken...';
    }
});
</script>
<?= $this->endSection() ?>
