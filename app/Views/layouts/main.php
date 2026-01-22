<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Remcom Dashboard' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3479CD;
            --primary-dark: #1F3B7A;
            --primary-soft: rgba(52, 121, 205, 0.12);
            --surface: #0E173A;
            --accent-mint: #4FDDC8;
            --accent-lilac: #A4B4FF;
            --text-dark: #0B1533;
            --text-muted: #7A89B5;
            --card-bg: #FFFFFF;
            --border-subtle: rgba(15, 23, 42, 0.08);
            --radius-xl: 28px;
            --radius-lg: 20px;
        }
        
        * {
            font-family: 'Parkinsans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #EFF3FF;
            margin: 0;
            padding: 0;
            color: var(--text-dark);
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(120deg, #09123A, #0D1F52);
            padding: 0.8rem 0;
            box-shadow: 0 12px 40px rgba(6, 13, 40, 0.45);
        }
        
        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #fff !important;
            font-size: 1.6rem;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
        }
        
        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4F8CFF, #24D3FB);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav-utilities {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .nav-quick-action {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 1.4rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        
        .nav-quick-action:hover {
            background: rgba(255,255,255,0.2);
            color: #fff;
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background-image: linear-gradient(120deg, #3479CD, #5D9BFF);
            border: none;
            font-weight: 600;
            color: #fff !important;
            box-shadow: 0 15px 35px rgba(52, 121, 205, 0.35);
            border-radius: 999px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(52, 121, 205, 0.4);
        }
        
        .btn-outline-dark,
        .btn-secondary,
        .btn-outline-secondary {
            border-radius: 999px;
            font-weight: 600;
        }
        
        /* Hero Section Styles */
        main {
            padding: 2.5rem 0 4rem;
        }
        
        .dashboard-shell {
            padding: 2rem;
            border-radius: 32px;
            background: #FFFFFF;
            box-shadow: 0 35px 80px rgba(15, 26, 67, 0.12);
        }
        
        .dashboard-hero {
            background: radial-gradient(circle at top left, rgba(255,255,255,0.15), transparent 45%), linear-gradient(135deg, #101D4A 0%, #152863 60%, #1F3B7A 100%);
            color: white;
            border-radius: 32px;
            padding: 2.75rem 3rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 2.5rem;
        }
        
        .dashboard-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.45;
            pointer-events: none;
        }
        
        .dashboard-hero .hero-meta {
            position: relative;
            z-index: 2;
        }
        
        .dashboard-hero h1 {
            font-size: clamp(2.4rem, 4vw, 3rem);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .dashboard-hero p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 1.5rem;
        }
        
        .dashboard-hero .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.35rem;
            border-radius: 18px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            text-decoration: none;
            min-width: 260px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .hero-btn strong {
            display: block;
            font-size: 1.05rem;
        }

        .hero-btn small {
            display: block;
            color: rgba(255,255,255,0.75);
            font-size: 0.85rem;
        }

        .hero-btn-primary {
            background: linear-gradient(120deg, #4F8CFF, #2B63BB);
            border-color: transparent;
            box-shadow: 0 20px 45px rgba(17, 36, 94, 0.45);
        }

        .hero-btn-ghost {
            background: rgba(16, 29, 74, 0.6);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px rgba(5, 10, 30, 0.35);
        }

        .hero-btn-icon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: rgba(255,255,255,0.18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.25);
        }

        .hero-btn-primary .hero-btn-icon {
            background: rgba(255,255,255,0.2);
            box-shadow: inset 0 0 0 2px rgba(255,255,255,0.25);
        }

        .hero-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 40%);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .hero-btn:hover::after {
            opacity: 1;
        }
        
        .btn-make-list {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-make-list:hover {
            background-color: #2B63BB;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 121, 205, 0.35);
        }
        
        .divider-text {
            text-align: center;
            margin: 1.5rem 0;
            color: #999;
            font-weight: 600;
        }
        
        .find-list-wrapper {
            position: relative;
        }
        
        .find-list-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .find-list-input {
            padding-left: 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            height: 50px;
            font-size: 1rem;
        }
        
        .find-list-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 121, 205, 0.2);
        }
        
        .how-it-works {
            color: #fff;
            margin-top: 2rem;
        }
        
        .how-it-works h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .step-number {
            background: #fff;
            color: var(--primary-color);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .step-text {
            font-size: 1.05rem;
        }
        
        .sinterklaas-section {
            margin-top: 2rem;
        }
        
        .sinterklaas-title {
            color: var(--yellow-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .btn-drawing-lots {
            background-color: var(--yellow-color);
            color: #333;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-drawing-lots:hover {
            background-color: #FFB300;
            color: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }
        
        .phone-mockup {
            position: relative;
            z-index: 2;
        }
        
        .phone-mockup img {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.2));
        }
        
        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
        }
        
        .stats-card {
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            color: white;
            background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.25), transparent 40%), linear-gradient(135deg, #3479CD, #5D9BFF);
            box-shadow: 0 25px 60px rgba(19, 54, 111, 0.35);
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }
        
        .stats-card p {
            margin: 0;
            color: rgba(255,255,255,0.85);
        }
        
        .glass-card,
        .card,
        .white-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            background: var(--card-bg);
            box-shadow: 0 16px 45px rgba(9, 12, 34, 0.08);
        }

        /* Data tables */
        .table-responsive {
            border-radius: var(--radius-lg);
            background: var(--card-bg);
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04);
        }

        .data-table {
            width: 100%;
            min-width: 720px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table thead th {
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            background: linear-gradient(120deg, #F8FAFF, #EEF2FF);
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .data-table th,
        .data-table td {
            padding: 0.95rem 1.25rem;
            vertical-align: middle;
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .data-table tbody tr {
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            transition: background 0.2s ease, box-shadow 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(52, 121, 205, 0.05);
            box-shadow: inset 0 0 0 1px rgba(52, 121, 205, 0.15);
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table td:last-child,
        .data-table th:last-child,
        .data-table td:nth-last-child(2),
        .data-table th:nth-last-child(2) {
            text-align: center;
            white-space: nowrap;
        }

        .data-table .btn {
            border-radius: 12px;
            padding: 0.35rem 0.75rem;
            min-width: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 0.25rem;
        }

        .data-table .badge-soft {
            font-size: 0.78rem;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
        }

        @media (max-width: 992px) {
            .table-responsive {
                border-radius: var(--radius-lg);
                overflow-x: auto;
            }

            .data-table {
                min-width: 600px;
            }
        }
        
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 55px rgba(12, 21, 54, 0.12);
        }
        
        .product-card {
            margin-bottom: 1rem;
        }
        
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        
        .category-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #f1f5f9;
            border-radius: 0.5rem;
            margin: 0.25rem;
            text-decoration: none;
            color: #334155;
            transition: background-color 0.2s;
        }
        
        .category-badge:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
        
        .list-card {
            margin-bottom: 1.5rem;
        }
        
        .list-card .card-body {
            padding: 1.5rem;
        }
        
        .btn-inline {
            padding: 0.45rem 1.25rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.4);
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
        
        .landing-footer {
            margin-top: 4rem;
            background: #050C3E;
            color: rgba(255,255,255,0.75);
            padding: 4rem 0 3rem;
        }

        .landing-footer a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
        }

        .landing-footer a:hover {
            color: #ffffff;
        }

        .footer-heading {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
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
        
        .alert {
            border-radius: 0.5rem;
        }
        
        /* Sidebar Menu */
        .sidebar-menu {
            background-color: #f8f8f8;
            padding: 1.5rem;
            border-radius: 8px;
        }
        
        .sidebar-menu ul li {
            margin-bottom: 1rem;
        }
        
        .sidebar-menu ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        
        .sidebar-menu ul li a:hover {
            color: var(--primary-color);
        }
        
        /* Login/Register Header */
        .auth-header {
            background-color: #fff;
            padding: 1rem 0;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 2rem;
        }
        
        .auth-header .form-control {
            border-radius: 6px;
        }
        
        .auth-header .btn {
            border-radius: 6px;
        }
        
        /* Product Card Enhancements */
        .product-card {
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-4px);
        }
        
        .product-card .card-body {
            padding: 1.25rem;
        }
        
        .product-card .card-title {
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.4;
        }
        
        .product-card .card-text {
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .product-card .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
        }
        
        .product-card .btn-primary:hover {
            background-color: #c41a1f;
            border-color: #c41a1f;
        }
        
        /* List View Enhancements */
        .list-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        
        .creator-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .creator-profile a {
            color: inherit;
            text-decoration: none;
        }
        
        .creator-profile a:hover {
            color: var(--primary-color);
        }
        
        .share-buttons .btn-group .btn {
            border-radius: 6px;
            margin-right: 0.5rem;
            font-weight: 500;
        }
        
        .share-buttons .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .alert-sm {
            padding: 0.5rem 0.75rem;
            margin-bottom: 0;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            color: #ccc;
        }
        
        /* Drag and Drop Styles */
        .sortable-ghost {
            opacity: 0.5;
            background-color: #f0f0f0;
            border: 2px dashed var(--primary-color);
        }
        
        .sortable-drag {
            opacity: 1;
        }
        
        #productList .card {
            cursor: grab;
            transition: all 0.2s ease;
        }
        
        #productList .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        #productList .card:active {
            cursor: grabbing;
        }
        
        .fa-grip-vertical {
            transition: color 0.2s ease;
        }
        
        #productList .card:hover .fa-grip-vertical {
            color: var(--primary-color) !important;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('index.php') ?>">
                <span class="brand-mark">R</span>
                Remcom
            </a>
            <div class="nav-utilities">
                <div class="d-none d-lg-flex gap-2">
                    <a class="nav-quick-action" href="<?= base_url('index.php/dashboard') ?>">
                        <i class="fas fa-gauge"></i> Dashboard
                    </a>
                    <a class="nav-quick-action" href="<?= base_url('index.php/drawings') ?>">
                        <i class="fas fa-dice"></i> Loten Trekken
                    </a>
                    <?php if ($user && $user['role'] === 'admin'): ?>
                        <a class="nav-quick-action" href="<?= base_url('index.php/admin') ?>">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    <?php endif; ?>
                </div>
                <div class="nav-actions d-flex flex-wrap gap-2 justify-content-end">
                    <?php if ($isLoggedIn): ?>
                        <a class="btn btn-primary" href="<?= base_url('index.php/dashboard/list/create') ?>">
                            <i class="fas fa-plus me-1"></i> Nieuwe Lijst
                        </a>
                        <a class="btn btn-outline-light" href="<?= base_url('index.php/logout') ?>">
                            Afmelden
                        </a>
                    <?php else: ?>
                        <a class="btn btn-outline-light" href="<?= base_url('index.php/register') ?>">Registreren</a>
                        <a class="btn btn-primary" href="<?= base_url('index.php/login') ?>">Inloggen</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->has('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="landing-footer" id="dashboard-footer">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h4 class="footer-heading">Remcom</h4>
                    <p>Een betrouwbaar platform dat veilige, transparante en moderne cadeau-oplossingen biedt voor families en vrienden.</p>
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

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="liveToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for easier DOM manipulation) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Toast Notification System -->
    <script>
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('liveToast');
            const toastBody = document.getElementById('toastMessage');
            
            // Set message
            toastBody.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + ' me-2"></i>' + message;
            
            // Set color based on type
            toastEl.className = 'toast align-items-center border-0 text-white bg-' + (type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info');
            
            // Show toast
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            toast.show();
        }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
