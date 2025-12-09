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
            --primary-color: #E31E24;
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
            background-color: #c41a1f;
            border-color: #c41a1f;
        }
        
        /* Hero Section Styles */
        .hero-section {
            background: linear-gradient(135deg, #E31E24 0%, #c41a1f 100%);
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
            color: #FFD54F;
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
            background-color: #c41a1f;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(227, 30, 36, 0.3);
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
            box-shadow: 0 0 0 0.2rem rgba(227, 30, 36, 0.1);
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
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        footer {
            background-color: #2c2c2c;
            color: #999;
            padding: 3rem 0 2rem;
            margin-top: 0;
        }
        
        footer a {
            color: #999;
            text-decoration: none;
        }
        
        footer a:hover {
            color: #fff;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        .footer-copyright {
            color: #666;
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
                        <?php if ($user && $user['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('index.php/admin') ?>">
                                    <i class="fas fa-cog"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('index.php/logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-dark btn-sm me-2" href="<?= base_url('index.php/register') ?>">Make a list</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-dark btn-sm" href="<?= base_url('index.php/login') ?>">Log in</a>
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
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <ul class="footer-links">
                        <li><a href="#">Frequently Asked Questions</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Drawing lots</a></li>
                        <li><a href="#">Shops</a></li>
                        <li><a href="#">About Lijstje.nl</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="app-badges">
                        <a href="#" class="d-inline-block me-2">
                            <img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83" alt="Download on App Store" style="height: 40px;">
                        </a>
                        <a href="#" class="d-inline-block">
                            <img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" alt="Get it on Google Play" style="height: 60px; margin-top: -10px;">
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <p class="footer-copyright mb-0">&copy; <?= date('Y') ?> LIJST.NL &nbsp;&bull;&nbsp; <a href="#">Terms of Use</a> &nbsp;&bull;&nbsp; <a href="#">Privacy Statement</a></p>
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
