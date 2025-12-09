<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <div class="sidebar-menu">
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('index.php/register') ?>">Make a list</a></li>
                    <li><a href="#">Find a list</a></li>
                    <li class="text-danger"><i class="fas fa-gift"></i> Drawing lots</li>
                    <li><a href="#">Shops</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="mb-4">Make a list</h2>
                    
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('index.php/register') ?>" id="registerForm">
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold">Username<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">lijst.nl/username</span>
                                <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username') ?>" required minlength="3" maxlength="100" placeholder="username">
                                <?php if (session('errors.username')): ?>
                                    <div class="invalid-feedback"><?= session('errors.username') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">General<span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= old('first_name') ?>" required placeholder="First name">
                                    <?php if (session('errors.first_name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= old('last_name') ?>" required placeholder="Surname">
                                    <?php if (session('errors.last_name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required placeholder="Your email address">
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control mb-3 <?= session('errors.password') ? 'is-invalid' : '' ?>" id="password" name="password" required minlength="8" placeholder="Password">
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                            <input type="password" class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>" id="password_confirm" name="password_confirm" required minlength="8" placeholder="Confirm password">
                            <?php if (session('errors.password_confirm')): ?>
                                <div class="invalid-feedback"><?= session('errors.password_confirm') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Keep me informed about my lists by email<br>
                                    <small class="text-muted">Of course, we handle this information with care. Read our <a href="#">privacy statement</a>.</small>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            Create <i class="fas fa-arrow-right ms-2"></i>
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
        errorDiv.textContent = 'Passwords do not match';
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
        errorDiv.textContent = 'Password must be at least 8 characters';
        passwordInput.parentNode.appendChild(errorDiv);
        hasError = true;
    }
    
    // Disable button to prevent double submission
    if (!hasError) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating account...';
    }
});
</script>
<?= $this->endSection() ?>
