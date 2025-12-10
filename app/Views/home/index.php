<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">Welkom bij Lijstje.nl</h1>
                <p class="hero-subtitle">de online verlanglijst</p>
                
                <div class="action-card">
                    <a href="<?= base_url('index.php/register') ?>" class="btn btn-make-list">Maak een lijst</a>
                    <div class="divider-text">OF</div>
                    <div class="find-list-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control find-list-input" placeholder="Vind een lijst" id="findListInput">
                    </div>
                </div>
                
                <div class="how-it-works">
                    <h3>Hoe het werkt...</h3>
                    <div class="step">
                        <span class="step-number">1</span>
                        <span class="step-text">Maak uw lijst of vind een lijst</span>
                    </div>
                    <div class="step">
                        <span class="step-number">2</span>
                        <span class="step-text">Voeg cadeaus toe van honderden winkels</span>
                    </div>
                    <div class="step">
                        <span class="step-number">3</span>
                        <span class="step-text">Deel met familie en vrienden!</span>
                    </div>
                </div>
                
                <div class="sinterklaas-section">
                    <p class="sinterklaas-title">Of voor Sinterklaas of Kerstmis:</p>
                    <a href="<?= base_url('index.php/register') ?>" class="btn btn-drawing-lots">Begin met loten trekken</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="phone-mockup">
                    <img src="<?= base_url('assets/images/phone-mockup.png') ?>" alt="Lijstje.nl on mobile" class="img-fluid" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="stats-section">
    <div class="container">
        <p class="stats-intro">Lijstje.nl heeft</p>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="stat-item">
                    <i class="fas fa-users stat-icon"></i>
                    <h3 class="stat-number"><?= number_format($totalUsers ?? 7074477) ?></h3>
                    <p class="stat-label">Gebruikers</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <i class="fas fa-gift stat-icon"></i>
                    <h3 class="stat-number"><?= number_format($totalPresents ?? 116740576) ?></h3>
                    <p class="stat-label">Cadeaus</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <i class="fas fa-birthday-cake stat-icon"></i>
                    <h3 class="stat-number"><?= number_format($birthdaysThisMonth ?? 16225) ?></h3>
                    <p class="stat-label">Verjaardagen deze maand</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Find a list functionality
document.getElementById('findListInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const username = this.value.trim();
        if (username) {
            // Redirect to user's list page
            window.location.href = '<?= base_url('index.php/find/') ?>' + encodeURIComponent(username);
        }
    }
});
</script>
<?= $this->endSection() ?>
