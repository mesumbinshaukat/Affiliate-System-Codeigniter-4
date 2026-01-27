<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .invite-hero {
        background: linear-gradient(135deg, #fdf2f8, #e0f2fe);
        border-radius: 30px;
        padding: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.3);
        box-shadow: 0 35px 70px rgba(15,23,42,0.15);
    }
    .invite-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 15% 20%, rgba(255,255,255,0.6), transparent 55%);
        opacity: 0.7;
    }
    .invite-hero__content {
        position: relative;
        z-index: 2;
    }
    .invite-cta-card {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 55px rgba(15,23,42,0.12);
    }
    .invite-cta-card .btn-primary {
        border-radius: 16px;
        font-weight: 600;
        padding: 14px;
    }
    .invite-summary {
        border-radius: 20px;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .invite-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 6px 16px;
        font-weight: 600;
        background: #1d4ed8;
        color: #fff;
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.1em;
    }
    @media (max-width: 767px) {
        .invite-hero {
            border-radius: 20px;
            padding: 24px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="invite-hero mb-4">
                <div class="invite-hero__content">
                    <div class="invite-pill mb-3">
                        <i class="fas fa-dice"></i>
                        Uitnodiging
                    </div>
                    <h1 class="display-5 fw-bold mb-3 text-navy">
                        Doe mee aan "<?= esc($drawing['title']) ?>"
                    </h1>
                    <?php if (!empty($drawing['description'])): ?>
                        <p class="lead text-muted mb-4">
                            <?= esc($drawing['description']) ?>
                        </p>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-3 text-muted">
                        <span><i class="fas fa-user me-2"></i><?= esc($drawing['creator_first_name'] ?? $drawing['first_name'] ?? 'Onbekend') ?></span>
                        <?php if (!empty($drawing['event_date'])): ?>
                            <span><i class="fas fa-calendar me-2"></i><?= date('d-m-Y', strtotime($drawing['event_date'])) ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-users me-2"></i>Loting via Lijst.wmcdev.nl</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-lg-6">
            <div class="card invite-cta-card">
                <div class="card-body p-4">
                    <h4 class="mb-3"><i class="fas fa-user-plus me-2 text-primary"></i> Meedoen</h4>
                    <p class="text-muted">Log in (of maak een account) en bevestig om deze loting te bekijken. Je komt daarna terecht op je dashboard met de uitnodiging.</p>
                    <a href="<?= base_url('index.php/drawings/join/' . $token) ?>" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i> Meld me aan
                    </a>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Je account is vereist zodat de organisator kan zien wie er mee doet en de loting eerlijk kan verlopen.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="p-4 invite-summary bg-white">
                <h5 class="fw-bold mb-3">Deze uitnodiging bevat</h5>
                <ul class="list-unstyled text-muted mb-4">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Unieke token-link</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Alleen voor geregistreerde gebruikers</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Slot op basis van toestemming</li>
                </ul>
                <small class="text-muted">Heb je de link van iemand anders gekregen? Geef hem niet door zonder toestemming van de organisator.</small>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
