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
            font-family: 'Parkinsans', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            font-optical-sizing: auto;
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
        }

        .landing-nav .brand {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
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
            position: relative;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            padding: 0.35rem 0.35rem 0.35rem 2.5rem;
            min-width: 220px;
            max-width: 340px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            overflow: hidden;
        }

        .nav-search svg {
            position: absolute;
            top: 50%;
            left: 0.9rem;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: 0.6;
        }

        .nav-search input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
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

        .landing-nav-collapse {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        @media (max-width: 992px) {
            .landing-nav-inner {
                flex-wrap: wrap;
            }

            .landing-nav-toggle {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
            }

            .landing-nav-collapse {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                display: none;
            }

            .landing-nav-collapse.show {
                display: flex;
            }

            .nav-right {
                flex-direction: column;
                width: 100%;
                align-items: stretch;
                gap: 1rem;
            }

            .nav-middle {
                width: 100%;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-middle .nav-link-cta {
                text-align: center;
                width: 100%;
                display: block;
                padding: 0.75rem;
                border-radius: 999px;
                background: rgba(255,255,255,0.08);
            }

            .nav-search {
                width: 100%;
                max-width: none;
            }

            .nav-actions {
                width: 100%;
                justify-content: stretch;
                gap: 0.75rem;
            }

            .nav-actions .btn-nav-primary {
                flex: 1;
                text-align: center;
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
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('index.php') ?>" class="brand">Remcom</a>
                <button class="landing-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavCollapse" aria-controls="landingNavCollapse" aria-expanded="false" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="landing-nav-collapse collapse" id="landingNavCollapse">
                <div class="nav-right">
                    <div class="nav-middle">
                        <a href="<?= base_url('index.php/register') ?>" class="nav-link-cta">Maak Een Verlanglijstje Aan</a>
                        <form class="nav-search" action="<?= base_url('index.php/search') ?>" method="get">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27A6 6 0 1 0 14 15.5l.27.28v.79L20 21.5 21.5 20l-6-6zm-5.5 0a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" fill="currentColor"/></svg>
                            <input type="text" name="q" placeholder="Zoek lijsten..." autocomplete="off" value="<?= esc($query ?? '') ?>">
                            <button type="submit">
                                <span>Zoeken</span>
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" fill="currentColor" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="nav-actions">
                        <?php if ($isLoggedIn ?? false): ?>
                            <a class="btn-nav-primary" href="<?= base_url('index.php/dashboard') ?>">Dashboard</a>
                        <?php else: ?>
                            <a class="btn-nav-primary" href="<?= base_url('index.php/login') ?>">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
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
    <script></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
