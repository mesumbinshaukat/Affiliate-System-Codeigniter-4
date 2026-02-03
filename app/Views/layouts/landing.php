<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Remcom | Maak cadeaus geven leuk' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/vendor/fontawesome/all.min.css') ?>" />
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

        @media (min-width: 992px) {
            .landing-nav-inner {
                flex-wrap: nowrap;
            }

            .landing-nav-shell {
                width: auto;
                flex: 0 0 auto;
            }

            .landing-nav-toggle {
                display: none !important;
            }

            .landing-nav-collapse {
                display: flex !important;
                align-items: center;
                justify-content: flex-end;
                gap: 1.5rem;
                position: static;
                width: auto;
                height: auto;
                background: transparent;
                border: none;
                padding: 0;
                box-shadow: none;
                transform: none;
            }

            .nav-right {
                flex-direction: row;
                align-items: center;
                gap: 1.25rem;
                width: auto;
            }

            .nav-middle {
                flex-direction: row;
                align-items: center;
                gap: 1rem;
            }

            .nav-middle .nav-link-cta {
                width: auto;
                background: transparent;
                padding: 0;
            }

            .nav-search {
                max-width: 320px;
            }

            .nav-actions {
                flex-direction: row;
                width: auto;
                gap: 0.75rem;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Parkinsans', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            font-optical-sizing: auto;
            margin: 0;
            background: var(--landing-bg);
            color: var(--landing-deep);
        }

        body.nav-open {
            overflow: hidden;
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
            background: #05021F;
            padding: 1.1rem 0;
            color: white;
            position: relative;
            z-index: 10;
        }

        .landing-nav-inner {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            justify-content: space-between;
            flex-wrap: nowrap;
            width: 100%;
        }

        .landing-nav-shell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 0.75rem;
            flex: 1 1 100%;
        }

        .landing-nav-panel {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            height: 100%;
        }

        .landing-nav-utilities {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1.25rem;
            width: 100%;
        }

        .landing-nav-shortcuts {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex: 1;
        }

        .landing-nav-shell .brand {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            flex: 0 0 auto;
        }

        .landing-nav-shell .landing-nav-toggle {
            margin-left: auto;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1.1rem;
            margin-left: auto;
        }

        .nav-middle {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .nav-link-cta {
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .nav-link-cta:hover {
            color: rgba(255,255,255,0.85);
        }

        .nav-search {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            padding: 0.35rem;
            min-width: 220px;
            max-width: 340px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            overflow: hidden;
        }

        .nav-search-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .nav-search-icon i {
            font-size: 15px;
        }

        .nav-search input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            padding-left: 0;
        }

        .nav-search input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .nav-search button {
            border: none;
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-weight: 600;
            border-radius: 999px;
            padding: 0.35rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: background 0.2s ease;
            flex-shrink: 0;
        }

        .nav-search button:hover {
            background: rgba(255,255,255,0.2);
        }

        .nav-search button svg {
            width: 14px;
            height: 14px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
        }

        .btn-nav-primary {
            border-radius: 999px;
            background: #3479CD;
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 1.4rem;
            border: none;
        }

        .btn-nav-primary:hover {
            opacity: 0.9;
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
            background: #05021F;
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
            font-size: 1.5rem;
        }

        .footer-links-inline {
            display: flex;
            gap: 2.5rem;
            flex-wrap: wrap;
            font-weight: 600;
        }

        .social-links {
            display: flex;
            gap: 0.85rem;
        }

        .social-links a {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: border-color 0.2s ease, color 0.2s ease;
        }

        .social-links a:hover {
            border-color: rgba(255,255,255,0.8);
            color: #fff;
        }

        .social-links img {
            width: 20px;
            height: 20px;
            display: block;
            object-fit: contain;
        }

        .social-links svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
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

        .landing-nav-toggle {
            display: none;
            border: 1px solid rgba(255,255,255,0.4);
            background: transparent;
            color: #fff;
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
        }

        .landing-nav-toggle .toggle-bars {
            display: inline-flex;
            flex-direction: column;
            gap: 4px;
            justify-content: center;
            align-items: center;
        }

        .landing-nav-toggle .toggle-bars span {
            display: block;
            width: 18px;
            height: 2px;
            background: #fff;
            border-radius: 999px;
        }

        .landing-close-btn {
            border: 1px solid rgba(255,255,255,0.4);
            background: transparent;
            color: #fff;
            border-radius: 999px;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .landing-close-btn span {
            font-size: 1.35rem;
            line-height: 1;
            margin-top: -2px;
        }

        .landing-nav-collapse {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .landing-nav-backdrop {
            display: none;
        }

        @media (max-width: 991.98px) {
            .landing-nav-inner {
                flex-wrap: wrap;
            }

            .landing-nav-shell {
                width: 100%;
            }

            .landing-nav-toggle {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            .landing-nav-collapse {
                position: fixed;
                top: 0;
                right: 0;
                width: 80vw;
                max-width: 360px;
                height: 100vh;
                background: linear-gradient(145deg, #0B1533, #101D4A);
                border-left: 1px solid rgba(255,255,255,0.08);
                padding: 4.25rem 1.5rem 2rem;
                overflow-y: auto;
                box-shadow: -18px 0 50px rgba(5, 9, 26, 0.6);
                transform: translateX(100%);
                transition: transform 0.25s ease;
                z-index: 1051;
                display: none;
            }

            .landing-nav-collapse.show {
                display: block;
                transform: translateX(0);
            }

            .landing-nav-panel {
                padding-bottom: 3rem;
            }

            .landing-nav-utilities {
                flex-direction: column;
                align-items: stretch;
                gap: 1.25rem;
                margin-top: 0;
                width: 100%;
            }

            .landing-nav-shortcuts {
                flex-direction: column;
                width: 100%;
                gap: 0.85rem;
                flex-wrap: wrap;
            }

            .landing-nav-shortcuts .nav-link-cta {
                width: 100%;
                justify-content: flex-start;
                padding: 1rem 1.2rem;
                border-radius: 18px;
                border: 1px solid rgba(255,255,255,0.2);
                background: rgba(255,255,255,0.1);
                font-size: 1rem;
            }

            .nav-right {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5rem;
            }

            .nav-middle {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .nav-actions,
            .landing-nav-actions {
                width: 100%;
                justify-content: stretch;
                gap: 0.75rem !important;
                flex-direction: column;
                flex-wrap: nowrap;
            }

            .nav-actions .btn,
            .landing-nav-actions .btn {
                flex: 1;
                text-align: center;
            }

            .nav-actions .btn-nav-primary {
                border-radius: 18px;
                padding: 0.9rem;
                font-size: 1rem;
            }

            .nav-search {
                width: 100%;
                margin: 0;
                border-radius: 24px;
                padding: 0.75rem;
                flex-direction: column;
                gap: 0.65rem;
                align-items: stretch;
            }

            .nav-search input {
                font-size: 1rem;
                width: 100%;
                padding-left: 0;
            }

            .nav-search button {
                border-radius: 18px;
                padding: 0.7rem 1.2rem;
                width: 100%;
            }

            .landing-nav-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(5, 10, 30, 0.65);
                z-index: 1050;
            }

            .landing-nav-backdrop:not(.active) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .landing-top-bar {
                text-align: center;
            }

            .landing-nav-inner {
                gap: 0.75rem;
            }

            .nav-right {
                gap: 0.75rem;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
  
    <nav class="landing-nav">
        <div class="container landing-nav-inner">
            <div class="landing-nav-shell">
                <a href="<?= base_url('') ?>" class="brand">Remcom</a>
                <button class="landing-nav-toggle" type="button" aria-label="Menu">
                    <span class="toggle-bars" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
            <div class="landing-nav-collapse" id="landingNavCollapse">
                <div class="landing-nav-panel">
                    <div class="d-lg-none d-flex justify-content-end mb-3">
                        <button type="button" class="landing-close-btn" aria-label="Sluiten">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="nav-right landing-nav-utilities">
                        <div class="nav-middle landing-nav-shortcuts">
                            <a href="<?= base_url('register') ?>" class="nav-link-cta">Maak Een Verlanglijstje Aan</a>
                            <form class="nav-search" action="<?= base_url('search') ?>" method="get">
                                <span class="nav-search-icon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="q" placeholder="Zoek lijsten..." autocomplete="off" value="<?= esc($query ?? '') ?>">
                                <button type="submit">
                                    <span>Zoeken</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" fill="currentColor" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="nav-actions landing-nav-actions">
                            <?php if ($isLoggedIn ?? false): ?>
                                <a class="btn-nav-primary" href="<?= base_url('dashboard') ?>">Dashboard</a>
                            <?php else: ?>
                                <a class="btn-nav-primary" href="<?= base_url('login') ?>">Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="landing-nav-backdrop" id="landingNavBackdrop"></div>

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
                            <a href="https://facebook.com" aria-label="Facebook" target="_blank" rel="noopener">
                                <img src="<?= base_url('public/media/braakbal10/facebook.png') ?>" alt="Facebook icon">
                            </a>
                            <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener">
                                <img src="<?= base_url('public/media/braakbal10/instagram.png') ?>" alt="Instagram icon">
                            </a>
                            <a href="https://google.com" aria-label="Google" target="_blank" rel="noopener">
                                <img src="<?= base_url('public/media/braakbal10/google.png') ?>" alt="Google icon">
                            </a>
                            <a href="https://youtube.com" aria-label="YouTube" target="_blank" rel="noopener">
                                <img src="<?= base_url('public/media/braakbal10/youtube.png') ?>" alt="YouTube icon">
                            </a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navCollapse = document.getElementById('landingNavCollapse');
            const navBackdrop = document.getElementById('landingNavBackdrop');
            const navToggle = document.querySelector('.landing-nav-toggle');
            const navClose = document.querySelector('.landing-close-btn');

            function openNav() {
                navCollapse.classList.add('show');
                navBackdrop.classList.add('active');
                document.body.classList.add('nav-open');
            }

            function closeNav() {
                navCollapse.classList.remove('show');
                navBackdrop.classList.remove('active');
                document.body.classList.remove('nav-open');
            }

            if (navToggle) {
                navToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (navCollapse.classList.contains('show')) {
                        closeNav();
                    } else {
                        openNav();
                    }
                });
            }

            if (navClose) {
                navClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeNav();
                });
            }

            if (navBackdrop) {
                navBackdrop.addEventListener('click', closeNav);
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
