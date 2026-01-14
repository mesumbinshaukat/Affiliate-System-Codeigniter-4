<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Remcom | Maak cadeaus geven leuk' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --landing-deep: #030A3D;
            --landing-navy: #0F1F58;
            --landing-blue: #2B49F1;
            --landing-blue-soft: #6B8BFF;
            --landing-sky: #E0ECFF;
            --landing-bg: #F3F6FF;
            --landing-card: #FFFFFF;
            --landing-border: #D8E2FF;
            --landing-muted: #7B8AAC;
            --landing-accent: #FFD56A;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            background: var(--landing-bg);
            color: var(--landing-deep);
        }

        a {
            text-decoration: none;
        }

        .landing-top-bar {
            background: #071146;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            padding: 0.55rem 0;
        }

        .landing-top-bar .top-link {
            color: #fff;
            font-weight: 600;
        }

        .landing-nav {
            background: linear-gradient(120deg, #050B34, #0F1F58);
            padding: 1.35rem 0;
            color: white;
            position: relative;
            z-index: 10;
        }

        .landing-nav .brand {
            font-size: 1.45rem;
            font-weight: 700;
            color: white;
        }

        .landing-nav .brand span {
            color: var(--landing-accent);
        }

        .landing-nav .nav-links {
            gap: 1.75rem;
        }

        .landing-nav .nav-links a {
            color: rgba(255,255,255,0.75);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .landing-nav .nav-links a.active {
            color: #fff;
        }

        .landing-nav .nav-actions {
            gap: 0.75rem;
        }

        .btn-pill {
            border-radius: 999px;
            padding: 0.65rem 1.55rem;
            font-weight: 600;
            font-size: 0.94rem;
        }

        .btn-pill-outline {
            border: 1px solid rgba(255,255,255,0.5);
            color: #fff;
        }

        .btn-pill-outline:hover {
            border-color: #fff;
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .btn-pill-solid {
            background: #fff;
            color: var(--landing-navy);
        }

        main {
            min-height: 60vh;
        }

        .image-placeholder {
            border: 2px dashed rgba(7,17,70,0.25);
            border-radius: 18px;
            background: #F4F7FF;
            color: var(--landing-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .landing-footer {
            background: #030A3D;
            color: rgba(255,255,255,0.82);
            padding: 3.5rem 0 2.5rem;
            margin-top: 4rem;
        }

        .landing-footer a {
            color: rgba(255,255,255,0.92);
            text-decoration: none;
        }

        .landing-footer .footer-heading {
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #fff;
            font-size: 1.1rem;
        }

        .footer-links-inline {
            display: flex;
            gap: 2.5rem;
            flex-wrap: wrap;
            font-weight: 600;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            font-size: 1.1rem;
        }

        .landing-footer hr {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 2rem 0 1.5rem;
        }

        .landing-footer .footer-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            font-size: 0.9rem;
        }

        @media (max-width: 992px) {
            .landing-nav .nav-links,
            .landing-nav .nav-actions {
                display: none !important;
            }
        }

        @media (max-width: 576px) {
            .landing-top-bar {
                text-align: center;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <header class="landing-top-bar">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <span>Maak moeiteloos verlanglijstjes voor elk moment.</span>
            <a class="top-link" href="<?= base_url('index.php/register') ?>">Begin vandaag nog gratis</a>
        </div>
    </header>
    <nav class="landing-nav">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
            <a href="<?= base_url('index.php') ?>" class="brand">Remcom</a>
            <div class="d-flex nav-links align-items-center">
                <a href="#hoe-het-werkt" class="active">Hoe het werkt</a>
                <a href="#waarom-remcom">Waarom Remcom</a>
                <a href="#verhalen">Verhalen</a>
                <a href="#faq">Veelgestelde Vragen</a>
            </div>
            <div class="d-flex nav-actions align-items-center">
                <?php if ($isLoggedIn ?? false): ?>
                    <a class="btn btn-pill btn-pill-outline" href="<?= base_url('index.php/dashboard') ?>">Ga naar dashboard</a>
                    <a class="btn btn-pill btn-pill-solid" href="<?= base_url('index.php/logout') ?>">Afmelden</a>
                <?php else: ?>
                    <a class="btn btn-pill btn-pill-outline" href="<?= base_url('index.php/login') ?>">Inloggen</a>
                    <a class="btn btn-pill btn-pill-solid" href="<?= base_url('index.php/register') ?>">Maak verlanglijstje</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="landing-footer" id="faq">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h4 class="footer-heading">Remcom</h4>
                    <p>Een betrouwbaar financieel platform dat veilige, transparante en moderne bankoplossingen biedt.</p>
                    <div class="d-flex align-items-center gap-3 mt-3">
                        <span>Volg ons:</span>
                        <div class="social-links">
                            <a href="https://facebook.com" aria-label="Facebook" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                            <a href="https://pinterest.com" aria-label="Pinterest" target="_blank" rel="noopener"><i class="fab fa-pinterest-p"></i></a>
                            <a href="https://google.com" aria-label="Google" target="_blank" rel="noopener"><i class="fab fa-google"></i></a>
                            <a href="https://youtube.com" aria-label="YouTube" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <nav class="footer-links-inline">
                        <a href="#">Winkel</a>
                        <a href="#">Over Ons</a>
                        <a href="#faq">Veelgestelde Vragen</a>
                        <a href="#contact">Contact</a>
                    </nav>
                </div>
            </div>
            <hr>
            <div class="footer-meta">
                <span>&copy; <?= date('Y') ?> Remcom. Alle rechten voorbehouden.</span>
                <div class="d-flex gap-4">
                    <a href="#">Gebruiksvoorwaarden</a>
                    <a href="#">Privacyverklaring</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
