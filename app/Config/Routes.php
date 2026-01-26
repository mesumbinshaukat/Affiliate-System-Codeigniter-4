<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('/search', 'Home::search');
$routes->get('/search/suggestions', 'Home::searchSuggestions');
$routes->get('/find', 'Home::find');
$routes->get('/find/(:segment)', 'Home::find/$1');
$routes->post('/find', 'Home::find');

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/logout', 'Auth::logout');

// Social Authentication Routes
// IMPORTANT: Specific routes MUST come before generic (:segment) pattern
$routes->get('auth/social/callback', 'SocialAuth::callback');
$routes->post('auth/social/callback', 'SocialAuth::callback');
$routes->get('auth/social/disconnect', 'SocialAuth::disconnect', ['filter' => 'auth']);
$routes->get('auth/social/(:segment)', 'SocialAuth::login/$1');
$routes->post('auth/social/(:segment)', 'SocialAuth::login/$1');

// Collaboration Routes
$routes->group('collaboration', ['filter' => 'auth'], function($routes) {
    $routes->post('invite', 'Collaboration::invite');
    $routes->get('accept/(:segment)', 'Collaboration::accept/$1');
    $routes->get('reject/(:segment)', 'Collaboration::reject/$1');
    $routes->post('cancel/(:num)', 'Collaboration::cancel/$1');
    $routes->post('remove', 'Collaboration::remove');
    $routes->post('leave', 'Collaboration::leave');
    $routes->get('list/(:num)/collaborators', 'Collaboration::getCollaborators/$1');
    $routes->post('permissions/update', 'Collaboration::updatePermissions');
});

// List Routes
$routes->get('/list/(:segment)', 'Lists::view/$1');
$routes->post('/list/(:segment)/access', 'Lists::attemptAccess/$1');
$routes->post('/list/access/(:segment)', 'Lists::attemptAccess/$1');
$routes->get('/list/(:segment)/share', 'Lists::share/$1');
$routes->post('/list/claim', 'Lists::claimProduct');
$routes->post('/list/unclaim', 'Lists::unclaimProduct');

// Contribution Routes
$routes->post('contribution/add', 'Contribution::add');
$routes->get('contribution/product/(:num)', 'Contribution::getProductContributions/$1');
$routes->post('contribution/toggle', 'Contribution::toggleGroupGift', ['filter' => 'auth']);

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

// Public invitation landing page (no auth required)
$routes->get('drawings/invite/(:segment)', 'Drawings::invite/$1');
$routes->get('drawings/join/(:segment)', 'Drawings::joinViaToken/$1');

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
    
    // Collaboration & Invitations
    $routes->get('invitations', 'Collaboration::invitations');
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
