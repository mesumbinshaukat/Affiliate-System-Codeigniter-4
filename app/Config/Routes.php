<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('/find', 'Home::find');
$routes->get('/find/(:segment)', 'Home::find/$1');
$routes->post('/find', 'Home::find');

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/logout', 'Auth::logout');

// List Routes
$routes->get('/list/(:segment)', 'Lists::view/$1');
$routes->get('/list/(:segment)/share', 'Lists::share/$1');
$routes->post('/list/claim', 'Lists::claimProduct');

// Drawings Routes (Loten Trekken)
$routes->group('drawings', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Drawings::index');
    $routes->get('create', 'Drawings::create');
    $routes->post('create', 'Drawings::create');
    $routes->get('edit/(:num)', 'Drawings::edit/$1');
    $routes->post('edit/(:num)', 'Drawings::edit/$1');
    $routes->get('view/(:num)', 'Drawings::view/$1');
    $routes->post('add-participant/(:num)', 'Drawings::addParticipant/$1');
    $routes->get('remove-participant/(:num)/(:num)', 'Drawings::removeParticipant/$1/$2');
    $routes->get('draw/(:num)', 'Drawings::draw/$1');
    $routes->get('accept-invitation/(:num)', 'Drawings::acceptInvitation/$1');
    $routes->get('decline-invitation/(:num)', 'Drawings::declineInvitation/$1');
});

// Public Drawings View (without auth) - for invitation links
$routes->get('/drawings/view/(:num)', 'Drawings::view/$1');
$routes->get('drawings/view/(:num)', 'Drawings::view/$1');

// Affiliate Tracking
$routes->get('/out/(:num)', 'Tracker::redirect/$1');

// User Profile Routes
$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('profile', 'User::profile');
    $routes->post('updateProfile', 'User::updateProfile');
});

// Dashboard Routes (User)
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('lists', 'Dashboard::lists');
    $routes->get('list/create', 'Dashboard::createList');
    $routes->post('list/create', 'Dashboard::createList');
    $routes->get('list/edit/(:num)', 'Dashboard::editList/$1');
    $routes->post('list/edit/(:num)', 'Dashboard::editList/$1');
    $routes->get('list/delete/(:num)', 'Dashboard::deleteList/$1');
    
    // Product Management
    $routes->get('products/search', 'Dashboard::searchProducts');
    $routes->get('list/products/(:num)', 'Dashboard::getListProducts/$1');
    $routes->post('product/add', 'Dashboard::addProduct');
    $routes->post('product/remove', 'Dashboard::removeProduct');
    $routes->post('product/positions', 'Dashboard::updateProductPositions');
    $routes->post('product/scrape', 'Dashboard::scrapeProduct');
    
    // Section Management
    $routes->post('section/add', 'Dashboard::addSection');
    $routes->post('section/update', 'Dashboard::updateSection');
    $routes->post('section/delete', 'Dashboard::deleteSection');
    $routes->post('product/move-to-section', 'Dashboard::moveProductToSection');
    
    // Analytics
    $routes->get('analytics', 'Dashboard::analytics');
    
    // Purchased Products
    $routes->get('purchased', 'Dashboard::purchasedProducts');
});

// Admin Routes
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Admin::index');
    
    // User Management
    $routes->get('users', 'Admin::users');
    $routes->get('user/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('user/edit/(:num)', 'Admin::editUser/$1');
    $routes->get('user/delete/(:num)', 'Admin::deleteUser/$1');
    
    // List Management
    $routes->get('lists', 'Admin::lists');
    $routes->get('list/featured/(:num)', 'Admin::toggleFeatured/$1');
    $routes->get('list/delete/(:num)', 'Admin::deleteListAdmin/$1');
    
    // Category Management
    $routes->get('categories', 'Admin::categories');
    $routes->get('category/create', 'Admin::createCategory');
    $routes->post('category/create', 'Admin::createCategory');
    $routes->get('category/edit/(:num)', 'Admin::editCategory/$1');
    $routes->post('category/edit/(:num)', 'Admin::editCategory/$1');
    $routes->get('category/delete/(:num)', 'Admin::deleteCategory/$1');
    
    // Analytics
    $routes->get('analytics', 'Admin::analytics');
    
    // Affiliate Sources
    $routes->get('affiliate-sources', 'Admin::affiliateSources');
    $routes->get('affiliate-source/toggle/(:num)', 'Admin::toggleSource/$1');
    
    // Settings
    $routes->get('settings', 'Admin::settings');
    $routes->post('settings', 'Admin::settings');
    
    // Drawing Management
    $routes->get('drawings', 'Admin::drawings');
    $routes->get('drawing/details/(:num)', 'Admin::drawingDetails/$1');
    $routes->get('drawing/delete/(:num)', 'Admin::deleteDrawing/$1');
});
