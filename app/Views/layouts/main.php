<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Lijstje.nl - Create and Share Product Lists' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3479CD;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --yellow-color: #FFC107;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        
        .navbar {
            background-color: #fff;
            box-shadow: none;
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #333 !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            height: 35px;
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #2B63BB;
            border-color: #2B63BB;
        }
        
        /* Hero Section Styles */
        .hero-section {
            background: linear-gradient(135deg, #3479CD 0%, #2B63BB 100%);
            min-height: 600px;
            padding: 60px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: 
                radial-circle(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .hero-title {
            color: #fff;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .hero-subtitle {
            color: #E4EEFF;
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
        }
        
        .action-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
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
        .stats-section {
            background-color: #f8f8f8;
            padding: 60px 0;
        }
        
        .stats-intro {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .stat-item {
            padding: 2rem;
        }
        
        .stat-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), #4F9BFF);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card h3 {
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
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
            justify-content: flex-end;
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
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('index.php') ?>">
                <i class="fas fa-list-ul" style="color: var(--primary-color);"></i> Lijstje.nl
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('index.php/dashboard') ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('index.php/drawings') ?>">
                                <i class="fas fa-dice"></i> Loten Trekken
                            </a>
                        </li>
                        <?php if ($user && $user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('index.php/admin') ?>">
                                    <i class="fas fa-cog"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('index.php/logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Afmelden
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-dark btn-sm me-2" href="<?= base_url('index.php/register') ?>">Maak een lijst</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-dark btn-sm" href="<?= base_url('index.php/login') ?>">Inloggen</a>
                        </li>
                    <?php endif; ?>
                </ul>
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
