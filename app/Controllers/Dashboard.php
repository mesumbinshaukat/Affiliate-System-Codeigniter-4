<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\ListProductModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ClickModel;
use App\Models\SalesModel;
use App\Models\ContributionModel;
use App\Libraries\ProductScraper;
use App\Libraries\BolComAPI;
use Config\Services;

class Dashboard extends BaseController
{
    public function __construct()
    {
        helper('text');
    }

    public function index()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $clickModel = new ClickModel();
        $salesModel = new SalesModel();
        $contributionModel = new ContributionModel();

        $userId = $this->session->get('user_id');

        $this->data['lists'] = $listModel->getUserLists($userId, true);
        $this->data['totalLists'] = count($this->data['lists']);
        $this->data['totalClicks'] = $clickModel->where('user_id', $userId)->countAllResults();
        
        // Add sales statistics
        $this->data['salesStats'] = $salesModel->getUserStatistics($userId);
        
        // Add contribution statistics for user's lists
        $userListIds = array_column($this->data['lists'], 'id');
        $totalContributions = 0;
        $contributionCount = 0;
        
        if (!empty($userListIds)) {
            $db = \Config\Database::connect();
            $builder = $db->table('contributions c');
            $builder->select('SUM(c.amount) as total, COUNT(c.id) as count')
                ->join('list_products lp', 'lp.id = c.list_product_id')
                ->whereIn('lp.list_id', $userListIds)
                ->where('c.status', 'completed');
            $result = $builder->get()->getRowArray();
            
            $totalContributions = (float) ($result['total'] ?? 0);
            $contributionCount = (int) ($result['count'] ?? 0);
        }
        
        $this->data['contributionStats'] = [
            'total_amount' => $totalContributions,
            'count' => $contributionCount,
        ];

        return view('dashboard/index', $this->data);
    }

    public function lists()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $userId = $this->session->get('user_id');

        // Get both owned and collaborated lists
        $this->data['lists'] = $listModel->getUserAccessibleLists($userId, true);

        return view('dashboard/lists', $this->data);
    }

    public function createList()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $categoryModel = new CategoryModel();
        $this->data['categories'] = $categoryModel->getActiveCategories();

        if (strtolower($this->request->getMethod()) === 'post') {
            $listModel = new ListModel();

            $title = $this->request->getPost('title');
            $slug = url_title($title, '-', true);

            // Check if slug exists
            $existingSlug = $listModel->where('slug', $slug)->first();
            if ($existingSlug) {
                $slug = $slug . '-' . time();
            }

            $data = [
                'user_id' => $this->session->get('user_id'),
                'category_id' => $this->request->getPost('category_id'),
                'title' => $title,
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'status' => 'published',
                'is_crossable' => $this->request->getPost('is_crossable') ? 1 : 0,
            ];

            if ($listModel->insert($data)) {
                $listId = $listModel->getInsertID();
                // If first_list flag is set, redirect to products tab
                $isFirstList = $this->request->getGet('first_list') === 'true';
                $redirectUrl = '/dashboard/list/edit/' . $listId;
                if ($isFirstList) {
                    $redirectUrl .= '?tab=products';
                }
                return redirect()->to($redirectUrl)->with('success', 'List created successfully');
            }

            $errors = $listModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        return view('dashboard/create_list', $this->data);
    }

    public function editList($listId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $categoryModel = new CategoryModel();
        $listProductModel = new ListProductModel();
        $listSectionModel = new \App\Models\ListSectionModel();

        $list = $listModel->find($listId);
        $userId = $this->session->get('user_id');

        // Updated permission check: allow owner or collaborators
        if (!$list || !$listModel->canUserEdit($listId, $userId)) {
            return redirect()->to('/dashboard')->with('error', 'List not found or you do not have permission to edit it');
        }

        // Check if user is the original owner
        $this->data['is_owner'] = $listModel->isUserOwner($listId, $userId);

        $this->data['list'] = $list;
        $this->data['categories'] = $categoryModel->getActiveCategories();
        $this->data['products'] = $listProductModel->getListProducts($listId);
        $this->data['sections'] = $listSectionModel->getListSectionsWithCounts($listId);
        
        // Get user age and gender for personalized suggestions
        $userAge = null;
        $userGender = null;
        $user = $this->session->get('user_id');
        if ($user) {
            $userModel = new \App\Models\UserModel();
            $userData = $userModel->find($user);
            if ($userData) {
                $userAge = $userModel->getAge($user);
                $userGender = $userData['gender'] ?? null;
            }
        }
        
        // Get personalized suggestions from age-based products JSON
        $personalizedSuggestions = [];
        $ageBasedProducts = new \App\Libraries\AgeBasedProducts();
        
        if ($ageBasedProducts->hasProducts()) {
            // Get random products for user's age range
            $personalizedSuggestions = $ageBasedProducts->getProductsForAge($userAge, 6);
        } else {
            // Fallback to API if JSON file doesn't exist
            $bolApi = new \App\Libraries\BolComAPI();
            $personalizedResult = $bolApi->getPersonalizedSuggestions($userAge, $userGender, 6);
            
            if ($personalizedResult['success']) {
                $personalizedSuggestions = $personalizedResult['products'] ?? [];
            }
        }
        
        // Get products from other lists in the same category (excluding products already in current list)
        $productModel = new \App\Models\ProductModel();
        $listProductModel = new ListProductModel();
        
        // Get IDs of products already in current list
        $currentListProducts = $listProductModel->where('list_id', $listId)->findAll();
        $currentProductIds = array_column($currentListProducts, 'product_id');
        
        // Get products from other lists in same category
        $suggestedProducts = [];
        if ($list['category_id']) {
            // Get category info including age restrictions
            $category = $categoryModel->find($list['category_id']);
            
            $otherLists = $listModel->select('lists.id')
                ->where('lists.category_id', $list['category_id'])
                ->where('lists.id !=', $listId)
                ->where('lists.status', 'published')
                ->orderBy('lists.views', 'DESC')
                ->limit(10)
                ->findAll();
            
            $otherListIds = array_column($otherLists, 'id');
            
            if (!empty($otherListIds)) {
                $suggestedProducts = $listProductModel->select('products.*')
                    ->join('products', 'products.id = list_products.product_id')
                    ->whereIn('list_products.list_id', $otherListIds)
                    ->groupBy('products.id')
                    ->orderBy('COUNT(list_products.id)', 'DESC')
                    ->limit(12)
                    ->findAll();
                
                // Filter out products already in current list
                $suggestedProducts = array_filter($suggestedProducts, function($product) use ($currentProductIds) {
                    return !in_array($product['id'], $currentProductIds);
                });
                
                // Filter by user age if category has age restrictions
                if ($userAge !== null && $category) {
                    $suggestedProducts = array_filter($suggestedProducts, function($product) use ($userAge, $category) {
                        $meetsMinAge = $category['min_age'] === null || $userAge >= $category['min_age'];
                        $meetsMaxAge = $category['max_age'] === null || $userAge <= $category['max_age'];
                        return $meetsMinAge && $meetsMaxAge;
                    });
                }
            }
        }
        
        $this->data['suggestedProducts'] = $suggestedProducts;
        $this->data['personalizedSuggestions'] = $personalizedSuggestions;
        $this->data['userAge'] = $userAge;
        $this->data['userGender'] = $userGender;

        if (strtolower($this->request->getMethod()) === 'post') {
            $title = $this->request->getPost('title');
            $slug = url_title($title, '-', true);

            // Check if slug exists (excluding current list)
            $existingSlug = $listModel->where('slug', $slug)
                ->where('id !=', $listId)
                ->first();
            if ($existingSlug) {
                $slug = $slug . '-' . time();
            }

            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'title' => $title,
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'is_crossable' => $this->request->getPost('is_crossable') ? 1 : 0,
            ];

            if ($listModel->update($listId, $data)) {
                return redirect()->back()->with('success', 'List updated successfully');
            }

            $errors = $listModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        return view('dashboard/edit_list', $this->data);
    }

    public function deleteList($listId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $listModel = new ListModel();
        $list = $listModel->find($listId);

        if (!$list || $list['user_id'] != $this->session->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'List not found');
        }

        if ($listModel->delete($listId)) {
            return redirect()->to('/dashboard/lists')->with('success', 'List deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete list');
    }

    public function searchProducts()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $query = $this->request->getGet('q');
        $limit = (int)($this->request->getGet('limit') ?? 10);
        $page = (int)($this->request->getGet('page') ?? 1);
        $sort = $this->request->getGet('sort') ?? 'RELEVANCE';
        $categoryId = $this->request->getGet('category_id');
        $rangeRefinement = $this->request->getGet('range_refinement');
        
        // Validate and sanitize parameters
        $limit = max(1, min($limit, 50)); // Between 1 and 50
        $page = max(1, $page);
        
        // Validate sort parameter
        $validSorts = ['RELEVANCE', 'PRICE_ASC', 'PRICE_DESC', 'POPULARITY', 'RATING_DESC'];
        if (!in_array($sort, $validSorts)) {
            $sort = 'RELEVANCE';
        }
        
        $results = [];
        $total = 0;
        $categories = [];
        $priceRefinements = [];

        if ($query) {
            $bolApi = new BolComAPI();
            
            // Use basic search - simple and reliable
            $offset = ($page - 1) * $limit;
            $response = $bolApi->searchProducts($query, $limit, $offset);
            
            if ($response['success']) {
                $results = $response['products'];
                $total = $response['total'] ?? count($results);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'products' => $results,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'categories' => $categories,
            'price_refinements' => $priceRefinements,
        ]);
    }

    public function addProduct()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $listId = $this->request->getPost('list_id');
            $productId = $this->request->getPost('product_id');
            $productData = $this->request->getPost('product');
            $sectionId = $this->request->getPost('section_id');

            // Validate required fields
            if (empty($listId) || (empty($productId) && empty($productData))) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields',
                ]);
            }

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($listId);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($listId, $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found or access denied',
                ]);
            }

            $productModel = new ProductModel();
            
            // If product_id is provided directly, use it (for suggested products)
            if (!empty($productId)) {
                // Verify product exists
                $product = $productModel->find($productId);
                if (!$product) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Product not found',
                    ]);
                }
            } else {
                // Handle new product from search
                $product = null;

                if (!empty($productData['external_id'])) {
                    $product = $productModel->findByExternalId($productData['external_id'], $productData['source']);
                }

                // Create product if not exists
                if (!$product) {
                    // Validate product data
                    if (empty($productData['title']) || empty($productData['affiliate_url'])) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Product title and affiliate URL are required',
                        ]);
                    }
                    
                    $productModel->insert($productData);
                    $productId = $productModel->getInsertID();
                } else {
                    $productId = $product['id'];
                }
            }

            // Check if product is already in this list (prevent duplicates)
            $listProductModel = new ListProductModel();
            $existingLink = $listProductModel->where('list_id', $listId)
                ->where('product_id', $productId)
                ->first();
            
            if ($existingLink) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product already exists in this list',
                ]);
            }

            // Get next position (within section if specified)
            $positionQuery = $listProductModel->where('list_id', $listId);
            if ($sectionId) {
                $positionQuery->where('section_id', $sectionId);
            }
            $maxPosition = $positionQuery->selectMax('position')->first();
            $position = ($maxPosition['position'] ?? 0) + 1;

            // Add to list with optional section
            $listProductModel->addProductToList($listId, $productId, $position, null, $sectionId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added successfully',
                'product_id' => $productId,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method',
        ]);
    }

    public function scrapeProduct()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $url = trim($this->request->getPost('url') ?? '');
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Voer een geldige product-URL in',
            ])->setStatusCode(400);
        }

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if (str_contains($host, 'bol.com')) {
            $bolResult = $this->handleBolProductScrape($url);

            if ($bolResult['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'product' => $bolResult['product'],
                    'source' => 'bol_api',
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $bolResult['message'] ?? 'Bol.com product kon niet worden opgehaald.',
            ])->setStatusCode($bolResult['status'] ?? 422);
        }

        $apiBase = rtrim(getenv('SCRAPER_API_BASE') ?: '', '/');
        $apiKey = getenv('SCRAPER_API_KEY') ?: '';

        if (empty($apiBase) || empty($apiKey)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Scraper configuratie ontbreekt. Neem contact op met de beheerder.',
            ])->setStatusCode(500);
        }

        $client = Services::curlrequest([
            'timeout' => 15,
            'connect_timeout' => 8,
            'http_errors' => false,
        ]);

        $endpoint = $apiBase . '/scrape?url=' . urlencode($url);
        $maxAttempts = 2;
        $lastErrorMessage = 'Scrapen mislukt. Controleer de URL en probeer opnieuw.';
        $lastStatusCode = 500;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = $client->get($endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Accept' => 'application/json',
                    ],
                ]);

                $status = $response->getStatusCode();
                $body = json_decode($response->getBody(), true);

                if ($status === 200 && !empty($body) && !empty($body['data'])) {
                    $scrapedData = $body['data'];
                    $product = $this->normalizeScrapedProduct($url, $scrapedData);

                    if (empty($product['title'])) {
                        $lastErrorMessage = 'Kon geen productgegevens extraheren. Controleer of de pagina openbaar toegankelijk is.';
                        $lastStatusCode = 422;
                    } else {
                        return $this->response->setJSON([
                            'success' => true,
                            'product' => $product,
                            'source' => 'scraper_hub',
                        ]);
                    }
                } else {
                    $lastErrorMessage = $body['error']['message'] ?? 'Scraper gaf een lege reactie terug.';
                    $lastStatusCode = $status >= 400 ? $status : 500;
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Scrape attempt ' . $attempt . ' failed: ' . $e->getMessage());
                $lastErrorMessage = 'Onverwachte fout tijdens het scrapen.';
                $lastStatusCode = 500;
            }

            if ($attempt < $maxAttempts) {
                usleep($attempt * 300000); // incremental backoff (0.3s, 0.6s, ...)
            }
        }

        // Primary scraper failed: attempt local fallback
        $fallbackScraper = new ProductScraper();
        $fallbackResult = $fallbackScraper->scrapeFallback($url);

        if ($fallbackResult['success']) {
            $fallbackData = [
                'title' => $fallbackResult['data']['title'] ?? '',
                'description' => $fallbackResult['data']['description'] ?? '',
                'image' => $fallbackResult['data']['image'] ?? '',
                'price' => $fallbackResult['data']['price'] ?? 0,
            ];

            $product = $this->normalizeScrapedProduct($url, $fallbackData);

            if (!empty($product['title'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'product' => $product,
                    'source' => 'fallback',
                ]);
            }
        }

        $fallbackReason = $fallbackResult['reason'] ?? 'Onbekende fout bij fallback scraper.';

        log_message(
            'error',
            sprintf(
                'ScrapeProduct failed for URL: %s. Primary error: %s. Fallback error: %s',
                $url,
                $lastErrorMessage,
                $fallbackReason
            )
        );

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Scrapen mislukt. Externe scraper: ' . $lastErrorMessage . ' | Fallback: ' . $fallbackReason,
        ])->setStatusCode($fallbackResult['success'] ? 500 : $lastStatusCode);
    }

    private function normalizeScrapedProduct(string $url, array $data): array
    {
        $title = trim($data['title'] ?? $data['meta']['title'] ?? '');
        $description = trim($data['description'] ?? substr($data['mainContent'] ?? '', 0, 240));
        $image = $this->extractImageUrl($data);
        $price = $data['price'] ?? null;
        if (!$price && !empty($data['mainContent'])) {
            $price = $this->extractPriceFromText($data['mainContent']);
        }
        if (!$price && !empty($description)) {
            $price = $this->extractPriceFromText($description);
        }
        $priceValue = $price ? $this->normalizePrice($price) : 0;

        return [
            'external_id' => 'scrape_' . md5($url),
            'title' => $title,
            'description' => $description,
            'image_url' => $image,
            'price' => $priceValue,
            'affiliate_url' => $url,
            'source' => parse_url($url, PHP_URL_HOST) ?? 'scraper',
            'ean' => '',
            'rating' => null,
        ];
    }

    private function extractPriceFromText(string $text): ?string
    {
        if (preg_match('/(?:€|\$)?\s?(\d{1,4}(?:[\.,]\d{2})?)/u', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function normalizePrice(string $price): float
    {
        $normalized = str_replace(['€', '$', ' '], '', $price);
        $normalized = str_replace(',', '.', $normalized);
        return (float) $normalized;
    }

    private function extractImageUrl(array $data): string
    {
        $candidates = [];

        if (isset($data['image'])) {
            $candidates[] = $data['image'];
        }
        if (isset($data['images'])) {
            $candidates[] = $data['images'];
        }
        if (isset($data['media']['images'])) {
            $candidates[] = $data['media']['images'];
        }

        foreach ($candidates as $candidate) {
            $url = $this->normalizeImageCandidate($candidate);
            if (!empty($url)) {
                return $url;
            }
        }

        return '';
    }

    private function normalizeImageCandidate($candidate): string
    {
        if (is_string($candidate)) {
            return trim($candidate);
        }

        if (is_object($candidate)) {
            $candidate = (array) $candidate;
        }

        if (is_array($candidate)) {
            if (isset($candidate['url'])) {
                return trim($candidate['url']);
            }
            if (isset($candidate['src'])) {
                return trim($candidate['src']);
            }
            if (isset($candidate[0])) {
                return $this->normalizeImageCandidate($candidate[0]);
            }
        }

        return '';
    }

    private function handleBolProductScrape(string $url): array
    {
        $bolApi = new BolComAPI();

        $bolId = $this->extractBolProductId($url);
        if ($bolId) {
            $product = $bolApi->getProduct($bolId);
            if ($product) {
                return ['success' => true, 'product' => $product];
            }
        }

        $searchTerm = $this->extractBolSearchTerm($url);
        if ($searchTerm) {
            $response = $bolApi->searchProducts($searchTerm, 1, 0);
            if (!empty($response['products'][0])) {
                return ['success' => true, 'product' => $response['products'][0]];
            }
        }

        return [
            'success' => false,
            'message' => 'Bol.com product werd niet gevonden. Controleer de link of probeer een andere.',
            'status' => 404,
        ];
    }

    private function extractBolProductId(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        if (preg_match('/(\d{9,})/', $path, $matches)) {
            return $matches[1];
        }

        parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
        if (!empty($query['productId']) && preg_match('/^\d{9,}$/', $query['productId'])) {
            return $query['productId'];
        }

        return null;
    }

    private function extractBolSearchTerm(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        if (preg_match('#/p/([^/]+)/#', $path, $matches)) {
            return str_replace(['-', '_'], ' ', urldecode($matches[1]));
        }

        return null;
    }

    public function getListProducts($listId)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        // Verify list access (owner or co-owner)
        $listModel = new ListModel();
        $list = $listModel->find($listId);
        $userId = $this->session->get('user_id');

        if (!$list || !$listModel->canUserEdit($listId, $userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'List not found or access denied',
            ]);
        }

        // Get products for this list
        $listProductModel = new ListProductModel();
        $products = $listProductModel->getListProducts($listId);

        return $this->response->setJSON([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function removeProduct()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $listId = $this->request->getPost('list_id');
            $productId = $this->request->getPost('product_id');

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($listId);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($listId, $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found',
                ]);
            }

            $listProductModel = new ListProductModel();
            $listProductModel->removeProductFromList($listId, $productId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product removed successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request',
        ]);
    }

    public function updateProductPositions()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $listId = $this->request->getPost('list_id');
            $positions = $this->request->getPost('positions');

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($listId);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($listId, $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found',
                ]);
            }

            $listProductModel = new ListProductModel();
            $listProductModel->updatePositions($listId, $positions);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Positions updated successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request',
        ]);
    }

    public function analytics()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $clickModel = new ClickModel();
        $salesModel = new SalesModel();
        $userId = $this->session->get('user_id');

        $this->data['clicks'] = $clickModel->getUserClicks($userId);
        $this->data['totalClicks'] = count($this->data['clicks']);
        
        // Add sales data
        $this->data['sales'] = $salesModel->getUserSales($userId);
        $this->data['salesStats'] = $salesModel->getUserStatistics($userId);

        return view('dashboard/analytics', $this->data);
    }

    public function purchasedProducts()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $userId = $this->session->get('user_id');
        $listModel = new ListModel();
        $listProductModel = new ListProductModel();

        // Get all claimed products from user's lists
        $db = \Config\Database::connect();
        $builder = $db->table('list_products lp');
        $builder->select('lp.id as list_product_id, lp.claimed_at, lp.claimed_by_subid, 
                         p.title as product_title, p.image_url, p.price, p.source,
                         l.id as list_id, l.title as list_title, l.slug as list_slug');
        $builder->join('products p', 'p.id = lp.product_id', 'left');
        $builder->join('lists l', 'l.id = lp.list_id', 'left');
        $builder->where('l.user_id', $userId);
        $builder->where('lp.claimed_at IS NOT NULL');
        $builder->orderBy('lp.claimed_at', 'DESC');
        
        $this->data['purchasedProducts'] = $builder->get()->getResultArray();
        
        // Statistics
        $this->data['totalPurchased'] = count($this->data['purchasedProducts']);
        
        // Count by list
        $listCounts = [];
        foreach ($this->data['purchasedProducts'] as $product) {
            $listId = $product['list_id'];
            if (!isset($listCounts[$listId])) {
                $listCounts[$listId] = [
                    'list_title' => $product['list_title'],
                    'count' => 0
                ];
            }
            $listCounts[$listId]['count']++;
        }
        $this->data['listCounts'] = $listCounts;

        return view('dashboard/purchased_products', $this->data);
    }

    public function addSection()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $listId = $this->request->getPost('list_id');
            $title = trim($this->request->getPost('title'));

            if (empty($listId) || empty($title)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List ID and title are required',
                ]);
            }

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($listId);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($listId, $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List not found or access denied',
                ]);
            }

            $listSectionModel = new \App\Models\ListSectionModel();
            $sectionId = $listSectionModel->addSection($listId, $title);

            if ($sectionId) {
                $section = $listSectionModel->find($sectionId);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Section added successfully',
                    'section' => $section,
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add section',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method',
        ]);
    }

    public function updateSection()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $sectionId = $this->request->getPost('section_id');
            $title = trim($this->request->getPost('title'));

            if (empty($sectionId) || empty($title)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section ID and title are required',
                ]);
            }

            $listSectionModel = new \App\Models\ListSectionModel();
            $section = $listSectionModel->find($sectionId);

            if (!$section) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section not found',
                ]);
            }

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($section['list_id']);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($section['list_id'], $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied',
                ]);
            }

            if ($listSectionModel->updateSectionTitle($sectionId, $title)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Section updated successfully',
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update section',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method',
        ]);
    }

    public function deleteSection()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $sectionId = $this->request->getPost('section_id');

            if (empty($sectionId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section ID is required',
                ]);
            }

            $listSectionModel = new \App\Models\ListSectionModel();
            $section = $listSectionModel->find($sectionId);

            if (!$section) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Section not found',
                ]);
            }

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($section['list_id']);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($section['list_id'], $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied',
                ]);
            }

            // Delete section (products will have section_id set to NULL via ON DELETE SET NULL)
            if ($listSectionModel->deleteSection($sectionId)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Section deleted successfully',
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete section',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method',
        ]);
    }

    public function moveProductToSection()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue',
            ]);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $listProductId = $this->request->getPost('list_product_id');
            $sectionId = $this->request->getPost('section_id'); // Can be null to remove from section

            if (empty($listProductId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'List product ID is required',
                ]);
            }

            $listProductModel = new ListProductModel();
            $listProduct = $listProductModel->find($listProductId);

            if (!$listProduct) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product not found in list',
                ]);
            }

            // Verify list access (owner or co-owner)
            $listModel = new ListModel();
            $list = $listModel->find($listProduct['list_id']);
            $userId = $this->session->get('user_id');

            if (!$list || !$listModel->canUserEdit($listProduct['list_id'], $userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied',
                ]);
            }

            if ($listProductModel->moveProductToSection($listProductId, $sectionId)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Product moved successfully',
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to move product',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method',
        ]);
    }
}
