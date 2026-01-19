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
            font-size: 1.5rem;
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
            padding: 0.4rem 1rem 0.4rem 2.5rem;
            min-width: 200px;
            max-width: 280px;
            width: 100%;
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
            width: 100%;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
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

        @media (max-width: 992px) {
            .nav-middle {
                display: none;
            }
            .landing-nav-inner {
                flex-wrap: wrap;
            }
            .nav-right {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .landing-top-bar {
                text-align: center;
            }
            .nav-right {
                width: 100%;
                justify-content: space-between;
            }
            .nav-actions {
                width: 100%;
                justify-content: space-between;
            }
            .nav-actions .btn-nav-outline,
            .nav-actions .btn-nav-primary {
                flex: 1;
                text-align: center;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
  
    <nav class="landing-nav">
        <div class="container landing-nav-inner">
            <a href="<?= base_url('index.php') ?>" class="brand">Remcom</a>
            <div class="nav-right">
                <div class="nav-middle">
                    <a href="<?= base_url('index.php/register') ?>" class="nav-link-cta">Maak Een Verlanglijstje Aan</a>
                    <label class="nav-search">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27A6 6 0 1 0 14 15.5l.27.28v.79L20 21.5 21.5 20l-6-6zm-5.5 0a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" fill="currentColor"/></svg>
                        <input type="text" placeholder="Zoekopdracht...">
                    </label>
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
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.5 8.5V6.75c0-.6.4-.75.7-.75H16V3h-2.3C11.6 3 10 4.7 10 6.7V8.5H8v3h2v9h3.3v-9h2.3l.4-3h-2.5z"/></svg>
                            </a>
                            <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 3h-9C5 3 3 5 3 7.5v9C3 19 5 21 7.5 21h9c2.5 0 4.5-2 4.5-4.5v-9C21 5 19 3 16.5 3zm3 12.5c0 1.7-1.3 3-3 3h-9c-1.7 0-3-1.3-3-3v-9c0-1.7 1.3-3 3-3h9c1.7 0 3 1.3 3 3v9z"/><path d="M12 8.5A3.5 3.5 0 1 0 12 15.5 3.5 3.5 0 1 0 12 8.5zm0 5.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/><circle cx="17" cy="7" r="1"/></svg>
                            </a>
                            <a href="https://google.com" aria-label="Google" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.2c0-.7-.1-1.4-.2-2.1H12v4h5.1c-.2 1.2-.9 2.3-2 3l-.1.1 3.3 2.6.2.1c1.9-1.7 2.5-4.2 2.5-7.7z"/><path d="M12 21c2.7 0 5-1 6.7-2.7l-3.6-2.8c-.8.5-1.8.8-3.1.8-2.4 0-4.5-1.6-5.3-3.8H3.1l-.1.1A9 9 0 0 0 12 21z"/><path d="M6.7 12c-.2-.6-.3-1.3-.3-2s.1-1.4.3-2L6.7 8H3.1a9 9 0 0 0 0 8l3.6-2.8z"/><path d="M12 5.5c1.5 0 2.8.5 3.8 1.4l2.9-2.9A9 9 0 0 0 12 3c-3.5 0-6.6 2-8.2 5l3.7 2.9C8.2 8.1 9.9 5.5 12 5.5z"/></svg>
                            </a>
                            <a href="https://youtube.com" aria-label="YouTube" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.6 7s-.2-1.4-.8-2c-.8-.8-1.8-.8-2.3-.9C15.2 4 12 4 12 4h-.1s-3.2 0-6.5.1c-.5.1-1.5.1-2.3.9-.6.6-.8 2-.8 2S2 8.7 2 10.4v1.2c0 1.7.2 3.4.2 3.4s.2 1.4.8 2c.8.8 1.9.7 2.4.8 1.8.2 7.6.3 7.6.3s3.2 0 6.5-.1c.5-.1 1.5-.1 2.3-.9.6-.6.8-2 .8-2s.2-1.7.2-3.4v-1.2c0-1.7-.2-3.4-.2-3.4zM10 14.7V7.8l5.6 3.4L10 14.7z"/></svg>
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
    <?= $this->renderSection('scripts') ?>
</body>
</html>
