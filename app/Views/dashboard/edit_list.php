<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    .personalized-list,
    .search-results-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .personalized-item,
    .search-result-item {
        border: 1px solid #e6e9ef;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        background: #fff;
        padding: 16px 20px;
        display: flex;
        gap: 18px;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .personalized-item:hover,
    .search-result-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
    }

    .personalized-item__image,
    .search-result-item__image {
        width: 140px;
        height: 140px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f5f8ff, #eef2ff);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .personalized-item__image img,
    .search-result-item__image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .personalized-item__body,
    .search-result-item__body {
        flex: 1;
    }

    .personalized-item__title,
    .search-result-item__title {
        font-weight: 600;
        font-size: 1rem;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .personalized-item__description,
    .search-result-item__description {
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 8px;
    }

    .personalized-item__meta,
    .search-result-item__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .personalized-item__actions,
    .search-result-item__actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 160px;
    }

    .personalized-pill {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 999px;
        background: #ecfeff;
        color: #0369a1;
        border: 1px solid #bae6fd;
        display: inline-flex;
        gap: 6px;
        align-items: center;
        font-weight: 600;
    }

    .search-result-item__checkbox {
        margin-right: 10px;
    }

    @media (max-width: 768px) {
        .personalized-item,
        .search-result-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .personalized-item__image,
        .search-result-item__image {
            width: 100%;
            height: 200px;
        }

        .personalized-item__actions,
        .search-result-item__actions {
            width: 100%;
        }
    }
</style>

<div class="container my-5">
    <h1 class="mb-4">Lijst Bewerken: <?= esc($list['title']) ?></h1>

    <ul class="nav nav-tabs mb-4" id="listTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                <i class="fas fa-info-circle"></i> Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                <i class="fas fa-box"></i> Producten
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="collaborators-tab" data-bs-toggle="tab" data-bs-target="#collaborators" type="button">
                <i class="fas fa-users"></i> Samenwerken
            </button>
        </li>
    </ul>

    <div class="tab-content" id="listTabsContent">
        <!-- Details Tab -->
        <div class="tab-pane fade" id="details" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('index.php/dashboard/list/edit/' . $list['id']) ?>">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Lijsttitel *</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?= esc($list['title']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categorie</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Selecteer een categorie</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" <?= $list['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                <?= esc($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Beschrijving</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= esc($list['description']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_crossable" name="is_crossable" value="1" <?= !empty($list['is_crossable']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_crossable">
                                            <strong>Sta toe dat items als gekocht gemarkeerd worden</strong>
                                            <small class="d-block text-muted">Bezoekers kunnen items afvinken nadat ze deze hebben gekocht (handig voor verlanglijstjes)</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Lijst Bijwerken</button>
                                    <a href="<?= base_url('index.php/dashboard/lists') ?>" class="btn btn-secondary">Terug naar Lijsten</a>
                                    <?php if ($list['status'] === 'published'): ?>
                                        <a href="<?= base_url('index.php/list/' . $list['slug']) ?>" class="btn btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> Openbare Weergave
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Tab -->
        <div class="tab-pane fade show active" id="products" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Section Management Panel -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-folder-open"></i> Secties Beheren
                                </h5>
                                <button class="btn btn-sm btn-primary" onclick="showAddSectionModal()">
                                    <i class="fas fa-plus"></i> Nieuwe Sectie
                                </button>
                            </div>
                            <p class="text-muted small mb-3">
                                Organiseer je producten in secties zoals "Sieraden", "Tech", "Lifetime Wensen", etc. Secties zijn optioneel.
                            </p>
                            
                            <div id="sectionsList">
                                <?php if (!empty($sections)): ?>
                                    <div class="list-group">
                                        <?php foreach ($sections as $section): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center" data-section-id="<?= $section['id'] ?>">
                                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                    <i class="fas fa-grip-vertical text-muted" style="cursor: move;"></i>
                                                    <i class="fas fa-folder text-primary"></i>
                                                    <span class="section-title-display fw-semibold"><?= esc($section['title']) ?></span>
                                                    <span class="badge bg-secondary"><?= $section['product_count'] ?? 0 ?> producten</span>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="editSection(<?= $section['id'] ?>, '<?= esc($section['title'], 'js') ?>')" title="Bewerken">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteSection(<?= $section['id'] ?>)" title="Verwijderen">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i> Nog geen secties aangemaakt. Klik op "Nieuwe Sectie" om te beginnen.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Search Products with Filters (MOVED TO TOP) -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Producten Toevoegen van Bol.com</h5>
                            
                            <!-- Search Input -->
                            <div class="row g-2 mb-3">
                                <div class="col">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="productSearch" placeholder="Zoeken naar producten (bijv. iPhone, laptop, boek)...">
                                        <button class="btn btn-primary" type="button" onclick="searchProducts(1)" id="searchBtn">
                                            <i class="fas fa-search"></i> Zoeken
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section Selector for New Products -->
                            <?php if (!empty($sections)): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-folder"></i> Voeg nieuwe producten toe aan sectie:
                                </label>
                                <select class="form-select" id="defaultSectionSelect">
                                    <option value="">Geen sectie (los product)</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section['id'] ?>">📁 <?= esc($section['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Dit wordt de standaard sectie voor nieuwe producten. Je kunt dit later wijzigen.</small>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Manual Scrape Toggle -->
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="scrapeToggle" onchange="toggleScrapeMode(this.checked)">
                                <label class="form-check-label" for="scrapeToggle">
                                    Product handmatig toevoegen via URL
                                </label>
                            </div>
                            
                            <div class="input-group mb-3 d-none" id="scrapeUrlGroup">
                                <input type="url" class="form-control" id="scrapeUrl" placeholder="Plak hier de volledige product-URL (bijv. https://winkel.nl/product)">
                                <button class="btn btn-outline-secondary" type="button" onclick="scrapeProductViaUrl()" id="scrapeBtn">
                                    <i class="fas fa-magic"></i> Product scrapen
                                </button>
                            </div>
                            
                            <div id="scrapeResult" class="mb-3"></div>
                            
                            <!-- Filters Section (Hidden by default, shown after search) -->
                            <div class="card card-light mb-3 d-none" id="filtersContainer">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="fas fa-filter"></i> Geavanceerde Filters</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Sorteren</label>
                                            <select class="form-select form-select-sm" id="sortSelect" onchange="applyFilters()">
                                                <option value="RELEVANCE">Relevantie</option>
                                                <option value="PRICE_ASC">Prijs: Laag naar Hoog</option>
                                                <option value="PRICE_DESC">Prijs: Hoog naar Laag</option>
                                                <option value="POPULARITY">Populariteit</option>
                                                <option value="RATING_DESC">Rating: Hoog naar Laag</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Min €</label>
                                            <input type="number" class="form-control form-control-sm" id="minPrice" placeholder="0" min="0" onchange="applyFilters()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Max €</label>
                                            <input type="number" class="form-control form-control-sm" id="maxPrice" placeholder="9999" min="0" onchange="applyFilters()">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-sm btn-outline-secondary w-100" onclick="resetFilters()">
                                                <i class="fas fa-redo"></i> Reset Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selected Products Counter -->
                            <div id="selectedCounter" class="alert alert-info d-none mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-check-circle"></i> <strong id="selectedCount">0</strong> product(en) geselecteerd</span>
                                    <div>
                                        <button class="btn btn-sm btn-success" onclick="addSelectedProducts()" id="addSelectedBtn">
                                            <i class="fas fa-plus"></i> Geselecteerde Toevoegen
                                        </button>
                                        <button class="btn btn-sm btn-secondary" onclick="clearSelection()">
                                            <i class="fas fa-times"></i> Wissen
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Search Results -->
                            <div id="searchResults" class="mt-3"></div>
                            
                            <!-- Pagination -->
                            <div id="paginationContainer" class="mt-3 d-none">
                                <nav>
                                    <ul class="pagination justify-content-center" id="pagination"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- Personalized Suggestions Section (Age-Based) -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-sparkles"></i> 
                                <?php if ($userAge && $userGender): ?>
                                    Persoonlijke Suggesties voor jouw Leeftijd (<?= $userAge ?>) en Geslacht
                                <?php elseif ($userAge): ?>
                                    Persoonlijke Suggesties voor jouw Leeftijd (<?= $userAge ?>)
                                <?php else: ?>
                                    Populaire Producten
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted small">
                                <?php if ($userAge && $userGender): ?>
                                    Producten speciaal geselecteerd op basis van jouw leeftijd en geslacht
                                <?php elseif ($userAge): ?>
                                    Producten speciaal geselecteerd op basis van jouw leeftijd
                                <?php else: ?>
                                    Populaire producten van Bol.com
                                <?php endif; ?>
                            </p>
                            
                            <?php if (!empty($personalizedSuggestions)): ?>
                                <div class="personalized-list">
                                    <?php foreach (array_slice($personalizedSuggestions, 0, 8) as $product): ?>
                                        <div class="personalized-item">
                                            <div class="personalized-item__image">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="<?= esc($product['image']) ?>" alt="<?= esc($product['title']) ?>">
                                                <?php else: ?>
                                                    <div class="text-muted text-center">
                                                        <i class="fas fa-image fa-2x"></i>
                                                        <p class="small mt-2 mb-0">Geen afbeelding</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="personalized-item__body">
                            <span class="personalized-pill">
                                <i class="fas fa-heart text-danger"></i>
                                <?= esc($product['category'] ?? 'Trending selectie') ?>
                            </span>
                                                <h6 class="personalized-item__title mt-2"><?= esc(character_limiter($product['title'], 60)) ?></h6>
                                                <p class="personalized-item__description"><?= esc(character_limiter($product['description'] ?? 'Aanbevolen voor jouw leeftijdsgroep.', 95)) ?></p>
                                                <div class="personalized-item__meta">
                                                    <?php if (!empty($product['price'])): ?>
                                                        <span class="badge bg-primary-subtle text-primary fw-semibold">€<?= number_format($product['price'], 2) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($product['source'])): ?>
                                                        <span class="badge bg-light text-muted"><i class="fas fa-store"></i> <?= esc($product['source']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="personalized-item__actions">
                                                <button class="btn btn-primary btn-sm" onclick="addSingleProduct(<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>)">
                                                    <i class="fas fa-plus"></i> Toevoegen
                                                </button>
                                                <a href="<?= esc($product['url'] ?? '#') ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                                    Bekijk
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i> 
                                    <?php if ($userAge || $userGender): ?>
                                        Geen persoonlijke suggesties beschikbaar op dit moment. Probeer producten handmatig toe te voegen via de zoekopdracht hierboven.
                                    <?php else: ?>
                                        Vul je profiel in (leeftijd en geslacht) voor persoonlijke suggesties. Je kunt ook producten handmatig toevoegen via de zoekopdracht hierboven.
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Current Products -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-grip-vertical"></i> Producten in deze Lijst
                                <small class="text-muted">(sleep om opnieuw in te delen)</small>
                            </h5>
                            <div id="productList" class="sortable-list">
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <div class="card mb-3" data-product-id="<?= $product['product_id'] ?>" data-list-product-id="<?= $product['list_product_id'] ?>">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2">
                                                        <?php if ($product['image_url']): ?>
                                                            <img src="<?= esc($product['image_url']) ?>" class="img-fluid rounded" alt="<?= esc($product['title']) ?>">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <h6><?= esc($product['title']) ?></h6>
                                                        <p class="text-muted mb-1 small"><?= esc(character_limiter($product['description'], 100)) ?></p>
                                                        <?php if ($product['price']): ?>
                                                            <strong class="text-primary">€<?= number_format($product['price'], 2) ?></strong>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Section Assignment -->
                                                        <div class="mt-2">
                                                            <select class="form-select form-select-sm" style="max-width: 250px;" onchange="moveProductToSection(<?= $product['list_product_id'] ?>, this.value)">
                                                                <option value="">Geen sectie</option>
                                                                <?php if (!empty($sections)): ?>
                                                                    <?php foreach ($sections as $section): ?>
                                                                        <option value="<?= $section['id'] ?>" <?= ($product['section_id'] ?? null) == $section['id'] ? 'selected' : '' ?>>
                                                                            📁 <?= esc($section['title']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>
                                                        
                                                        <!-- Group Gift Toggle -->
                                                        <div class="mt-3 p-2 bg-light rounded">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="groupGift<?= $product['list_product_id'] ?>" 
                                                                       <?= !empty($product['is_group_gift']) ? 'checked' : '' ?>
                                                                       onchange="toggleGroupGift(<?= $product['list_product_id'] ?>, this.checked)">
                                                                <label class="form-check-label" for="groupGift<?= $product['list_product_id'] ?>">
                                                                    <i class="fas fa-users"></i> Groepscadeau
                                                                </label>
                                                            </div>
                                                            <div id="groupGiftAmount<?= $product['list_product_id'] ?>" class="mt-2" style="display: <?= !empty($product['is_group_gift']) ? 'block' : 'none' ?>">
                                                                <label class="form-label small mb-1">Doelbedrag:</label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">€</span>
                                                                    <input type="number" class="form-control" id="targetAmount<?= $product['list_product_id'] ?>" 
                                                                           value="<?= !empty($product['target_amount']) ? $product['target_amount'] : ($product['price'] ?? '') ?>" 
                                                                           step="0.01" min="0.01"
                                                                           onchange="updateGroupGiftAmount(<?= $product['list_product_id'] ?>, this.value)">
                                                                </div>
                                                                <small class="text-muted">Mensen kunnen geldbedragen bijdragen</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-end">
                                                        <button class="btn btn-sm btn-danger" onclick="removeProduct(<?= $product['product_id'] ?>)">
                                                            <i class="fas fa-trash"></i> Verwijderen
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Nog geen producten toegevoegd. Zoek en voeg producten van Bol.com toe.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collaborators Tab -->
        <div class="tab-pane fade" id="collaborators" role="tabpanel">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <?= view('partials/collaborator_management', ['list' => $list, 'is_owner' => $is_owner ?? false]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const listId = <?= $list['id'] ?>;
let selectedProducts = new Map();
let currentSearchQuery = '';
let currentPage = 1;
let totalResults = 0;
const resultsPerPage = 10;
let priceRefinementId = null;
let allFetchedProducts = []; // Store all fetched products for client-side filtering
let manualScrapeMode = false;
let scrapedProductData = null;

// Apply filters to already-fetched products
function applyFilters() {
    if (allFetchedProducts.length === 0) {
        showToast('Voer eerst een zoekopdracht uit', 'warning');
        return;
    }
    
    // Reset to page 1 when filters change
    currentPage = 1;
    renderFilteredProducts();
}

// Reset all filters
function resetFilters() {
    document.getElementById('sortSelect').value = 'RELEVANCE';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    currentPage = 1;
    renderFilteredProducts();
}

// Filter products based on current filter values
function getFilteredProducts() {
    let filtered = [...allFetchedProducts];
    
    const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice').value) || 99999;
    const sort = document.getElementById('sortSelect').value || 'RELEVANCE';
    
    // Apply price filter
    filtered = filtered.filter(product => {
        const price = parseFloat(product.price) || 0;
        return price >= minPrice && price <= maxPrice;
    });
    
    // Apply sort
    if (sort === 'PRICE_ASC') {
        filtered.sort((a, b) => (parseFloat(a.price) || 0) - (parseFloat(b.price) || 0));
    } else if (sort === 'PRICE_DESC') {
        filtered.sort((a, b) => (parseFloat(b.price) || 0) - (parseFloat(a.price) || 0));
    } else if (sort === 'RATING_DESC') {
        filtered.sort((a, b) => (parseFloat(b.rating) || 0) - (parseFloat(a.rating) || 0));
    }
    // RELEVANCE and POPULARITY keep original order
    
    return filtered;
}

// Render filtered products with pagination
function renderFilteredProducts() {
    const filtered = getFilteredProducts();
    totalResults = filtered.length;
    
    const start = (currentPage - 1) * resultsPerPage;
    const end = start + resultsPerPage;
    const pageProducts = filtered.slice(start, end);
    
    if (pageProducts.length > 0) {
        renderSearchResults(pageProducts);
        renderPagination();
    } else {
        document.getElementById('searchResults').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> Geen producten gevonden met de huidige filters.
            </div>
        `;
        document.getElementById('paginationContainer').classList.add('d-none');
    }
    renderPagination();
}

// Search products with pagination and filters
function searchProducts(page = 1) {
    if (manualScrapeMode) {
        showToast('Schakel de URL-modus uit om weer te zoeken in Bol.com.', 'warning');
        return;
    }
    const query = document.getElementById('productSearch').value.trim();
    if (!query) {
        showToast('Voer een zoekterm in', 'warning');
        return;
    }

    currentSearchQuery = query;
    currentPage = page;
    const limit = 50; // Fetch more products for client-side filtering

    // Show loading
    const searchBtn = document.getElementById('searchBtn');
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Zoeken...';
    
    document.getElementById('searchResults').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Laden...</span></div><p class="mt-2 text-muted">Bol.com doorzoeken...</p></div>';

    // Build URL without filters - fetch all results for client-side filtering
    let url = `<?= base_url('index.php/dashboard/products/search') ?>?q=${encodeURIComponent(query)}&limit=${limit}&page=1`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            searchBtn.disabled = false;
            searchBtn.innerHTML = '<i class="fas fa-search"></i> Zoeken';
            
            // Show filters container after first successful search
            document.getElementById('filtersContainer').classList.remove('d-none');
            
            if (data.success && data.products && data.products.length > 0) {
                // Store all fetched products for client-side filtering
                allFetchedProducts = data.products;
                totalResults = data.products.length;
                
                // Reset filters to default
                document.getElementById('sortSelect').value = 'RELEVANCE';
                document.getElementById('minPrice').value = '';
                document.getElementById('maxPrice').value = '';
                currentPage = 1;
                
                // Render filtered products
                renderFilteredProducts();
            } else {
                allFetchedProducts = [];
                document.getElementById('searchResults').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> Geen producten gevonden voor "<strong>${escapeHtml(query)}</strong>".
                        <br><small>Probeer andere zoektermen of controleer de spelling.</small>
                    </div>
                `;
                document.getElementById('paginationContainer').classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            searchBtn.disabled = false;
            searchBtn.innerHTML = '<i class="fas fa-search"></i> Zoeken';
            document.getElementById('searchResults').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Fout bij het zoeken naar producten. Probeer het opnieuw.
                </div>
            `;
        });
}

// Update category filter dropdown from API response
function updateCategoryFilter(categories) {
    const select = document.getElementById('categorySelect');
    const currentValue = select.value;
    
    // Clear existing options except the first one
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    // Add categories from API response
    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.categoryId;
        option.textContent = `${cat.categoryName} (${cat.productCount})`;
        select.appendChild(option);
    });
    
    // Restore previous selection if it still exists
    if (currentValue && Array.from(select.options).some(opt => opt.value === currentValue)) {
        select.value = currentValue;
    }
}

// Render search results with checkboxes
function renderSearchResults(products) {
    let html = '<div class="search-results-list">';
    
    products.forEach(product => {
        const isSelected = selectedProducts.has(product.external_id);
        const checkboxId = `product-${product.external_id}`;
        const ratingBadge = product.rating ? `<span class="badge bg-warning text-dark"><i class="fas fa-star"></i> ${product.rating}</span>` : '';
        const eanBadge = product.ean ? `<span class="badge bg-light text-muted">EAN: ${escapeHtml(product.ean)}</span>` : '';
        
        html += `
            <div class="search-result-item" id="result-${product.external_id}">
                <div class="search-result-item__checkbox">
                    <div class="form-check">
                        <input class="form-check-input product-checkbox" type="checkbox"
                               id="${checkboxId}"
                               ${isSelected ? 'checked' : ''}
                               onchange="toggleProductSelection('${product.external_id}', this.checked)">
                        <label class="form-check-label" for="${checkboxId}"></label>
                    </div>
                </div>
                <div class="search-result-item__image">
                    ${product.image_url ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.title)}">` : '<div class="text-muted text-center w-100"><i class="fas fa-image fa-2x"></i><p class="small mb-0 mt-2">Geen afbeelding</p></div>'}
                </div>
                <div class="search-result-item__body">
                    <div class="search-result-item__title">${escapeHtml(product.title)}</div>
                    ${product.description ? `<p class="search-result-item__description">${escapeHtml(product.description.substring(0, 110))}${product.description.length > 110 ? '...' : ''}</p>` : '<p class="search-result-item__description text-muted mb-2">Geen beschrijving beschikbaar.</p>'}
                    <div class="search-result-item__meta">
                        ${product.price ? `<span class="badge bg-primary-subtle text-primary fw-semibold">€${parseFloat(product.price).toFixed(2)}</span>` : ''}
                        ${ratingBadge}
                        ${eanBadge}
                    </div>
                </div>
                <div class="search-result-item__actions">
                    <button class="btn btn-outline-primary btn-sm"
                            data-product="${encodeURIComponent(JSON.stringify(product))}"
                            onclick="addSingleProductFromButton(this)"
                            title="Voeg dit product toe">
                        <i class="fas fa-plus"></i> Voeg nu toe
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Add select all option
    const selectAllHtml = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)">
                <label class="form-check-label" for="selectAll">
                    <strong>Select all on this page</strong>
                </label>
            </div>
            <small class="text-muted">Showing ${products.length} of ${totalResults} results</small>
        </div>
    `;
    
    document.getElementById('searchResults').innerHTML = selectAllHtml + html;
}

// Toggle manual scrape mode
function toggleScrapeMode(isEnabled) {
    manualScrapeMode = isEnabled;
    const group = document.getElementById('scrapeUrlGroup');
    group.classList.toggle('d-none', !isEnabled);
    const searchInput = document.getElementById('productSearch');
    const searchBtn = document.getElementById('searchBtn');
    searchInput.disabled = isEnabled;
    searchBtn.disabled = isEnabled;
    scrapedProductData = null;
    document.getElementById('scrapeResult').innerHTML = '';
    if (!isEnabled) {
        document.getElementById('scrapeUrl').value = '';
    } else {
        showToast('URL-modus ingeschakeld. Plak een productlink om te scrapen.', 'info');
    }
}

function scrapeProductViaUrl() {
    if (!manualScrapeMode) {
        showToast('Schakel de URL-modus in om een product te scrapen.', 'warning');
        return;
    }
    const urlInput = document.getElementById('scrapeUrl');
    const productUrl = urlInput.value.trim();
    if (!productUrl) {
        showToast('Voer eerst een geldige product-URL in.', 'warning');
        return;
    }
    const scrapeBtn = document.getElementById('scrapeBtn');
    scrapeBtn.disabled = true;
    const originalHtml = scrapeBtn.innerHTML;
    scrapeBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Scrapen...';
    document.getElementById('scrapeResult').innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-circle-notch fa-spin"></i> Productpagina uitlezen...
        </div>
    `;

    fetch('<?= base_url('index.php/dashboard/product/scrape') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ url: productUrl })
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        scrapeBtn.disabled = false;
        scrapeBtn.innerHTML = originalHtml;

        if (!body.success) {
            document.getElementById('scrapeResult').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-triangle-exclamation"></i> ${escapeHtml(body.message || 'Scrapen mislukt.')}
                </div>
            `;
            scrapedProductData = null;
            return;
        }

        scrapedProductData = body.product;
        renderScrapeResult(body.product);
    })
    .catch(error => {
        console.error(error);
        scrapeBtn.disabled = false;
        scrapeBtn.innerHTML = originalHtml;
        document.getElementById('scrapeResult').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-triangle-exclamation"></i> Onverwachte fout bij het scrapen. Probeer het opnieuw.
            </div>
        `;
        scrapedProductData = null;
    });
}

function renderScrapeResult(product) {
    const description = product.description ? escapeHtml(product.description) : 'Geen beschrijving beschikbaar.';
    document.getElementById('scrapeResult').innerHTML = `
        <div class="search-result-item border border-success">
            <div class="search-result-item__image">
                ${product.image_url ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.title)}">`
                : '<div class="text-muted text-center w-100"><i class="fas fa-image fa-2x"></i><p class="small mb-0 mt-2">Geen afbeelding</p></div>'}
            </div>
            <div class="search-result-item__body">
                <div class="search-result-item__title">${escapeHtml(product.title)}</div>
                <p class="search-result-item__description">${description}</p>
                <div class="search-result-item__meta">
                    ${product.price ? `<span class="badge bg-primary-subtle text-primary fw-semibold">€${parseFloat(product.price).toFixed(2)}</span>` : ''}
                    <span class="badge bg-light text-muted"><i class="fas fa-globe"></i> ${escapeHtml(product.source || 'Extern')}</span>
                </div>
            </div>
            <div class="search-result-item__actions">
                <button class="btn btn-primary btn-sm" onclick="addScrapedProduct(this)">
                    <i class="fas fa-plus"></i> Toevoegen
                </button>
                <a href="${escapeHtml(product.affiliate_url)}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    Bekijk bron
                </a>
            </div>
        </div>
    `;
}

function addScrapedProduct(button) {
    if (!scrapedProductData) {
        showToast('Er is geen gescrapet product om toe te voegen.', 'warning');
        return;
    }
    addSingleProduct(scrapedProductData, button);
}
// Toggle product selection
function toggleProductSelection(productId, isSelected) {
    if (isSelected) {
        // Find product from all fetched products
        const product = allFetchedProducts.find(p => p.external_id === productId);
        if (product) {
            selectedProducts.set(productId, product);
            updateSelectedCounter();
        }
    } else {
        selectedProducts.delete(productId);
        updateSelectedCounter();
    }
}

// Toggle select all - instantly add all products on current page
function toggleSelectAll(isSelected) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    if (isSelected) {
        // Get filtered products for current page
        const filtered = getFilteredProducts();
        const start = (currentPage - 1) * resultsPerPage;
        const end = start + resultsPerPage;
        const pageProducts = filtered.slice(start, end);
        
        // Add all products on this page to selectedProducts
        pageProducts.forEach(product => {
            selectedProducts.set(product.external_id, product);
        });
        
        // Check all checkboxes
        checkboxes.forEach(checkbox => checkbox.checked = true);
        updateSelectedCounter();
    } else {
        // Uncheck all and clear selection
        checkboxes.forEach(checkbox => checkbox.checked = false);
        selectedProducts.clear();
        updateSelectedCounter();
    }
}

// Update selected counter
function updateSelectedCounter() {
    const count = selectedProducts.size;
    const counter = document.getElementById('selectedCounter');
    const countSpan = document.getElementById('selectedCount');
    
    countSpan.textContent = count;
    
    if (count > 0) {
        counter.classList.remove('d-none');
    } else {
        counter.classList.add('d-none');
    }
}

// Clear selection
function clearSelection() {
    selectedProducts.clear();
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelectedCounter();
}

// Add selected products in batch
function addSelectedProducts() {
    if (selectedProducts.size === 0) {
        showToast('Please select at least one product', 'warning');
        return;
    }
    
    const addBtn = document.getElementById('addSelectedBtn');
    addBtn.disabled = true;
    addBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
    
    const productsArray = Array.from(selectedProducts.values());
    let addedCount = 0;
    let failedCount = 0;
    
    // Add products sequentially to avoid race conditions
    const addNext = (index) => {
        if (index >= productsArray.length) {
            // All done
            addBtn.disabled = false;
            addBtn.innerHTML = '<i class="fas fa-plus"></i> Add Selected';
            
            if (failedCount === 0) {
                showToast(`Successfully added ${addedCount} product(s)!`, 'success');
                // Clear selection and refresh product list
                clearSelection();
                refreshProductList();
            } else {
                showToast(`Added ${addedCount} product(s). ${failedCount} failed.`, 'warning');
                clearSelection();
                refreshProductList();
            }
            return;
        }
        
        const product = productsArray[index];
        
        fetch('<?= base_url('index.php/dashboard/product/add') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                list_id: listId,
                'product[external_id]': product.external_id,
                'product[title]': product.title,
                'product[description]': product.description || '',
                'product[image_url]': product.image_url || '',
                'product[price]': product.price || 0,
                'product[affiliate_url]': product.affiliate_url,
                'product[source]': product.source,
                'product[ean]': product.ean || '',
                section_id: document.getElementById('defaultSectionSelect').value,
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    addedCount++;
                } else {
                    failedCount++;
                    console.log(`Failed to add product: ${product.title} - ${data.message}`);
                }
            } catch (e) {
                console.error('Invalid JSON response:', text);
                failedCount++;
            }
            // Add next product
            addNext(index + 1);
        })
        .catch(error => {
            console.error('Error:', error);
            failedCount++;
            // Continue with next product
            addNext(index + 1);
        });
    };
    
    // Start adding
    addNext(0);
}

// Handle add button clicks safely
function addSingleProductFromButton(button) {
    const encoded = button.getAttribute('data-product');
    if (!encoded) {
        showToast('Productgegevens ontbreken', 'error');
        return;
    }

    try {
        const product = JSON.parse(decodeURIComponent(encoded));
        addSingleProduct(product, button);
    } catch (e) {
        console.error('Unable to parse product data', e);
        showToast('Ongeldige productgegevens', 'error');
    }
}

// Add single product immediately
function addSingleProduct(product, triggerBtn = null) {
    const btn = triggerBtn || (event?.target ? event.target.closest('button') : null);
    const originalHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }
    
    // Normalize product data - handle both age-based and search result formats
    const normalizedProduct = {
        external_id: product.external_id || product.id || '',
        title: product.title || '',
        description: product.description || '',
        image_url: product.image_url || product.image || '',
        price: product.price || 0,
        affiliate_url: product.affiliate_url || product.url || '',
        source: product.source || (product.affiliate_url ? new URL(product.affiliate_url).hostname : 'extern'),
        ean: product.ean || '',
    };
    
    // Get selected section (if any)
    const sectionSelect = document.getElementById('defaultSectionSelect');
    const sectionId = sectionSelect ? sectionSelect.value : '';
    
    const params = {
        list_id: listId,
        'product[external_id]': normalizedProduct.external_id,
        'product[title]': normalizedProduct.title,
        'product[description]': normalizedProduct.description,
        'product[image_url]': normalizedProduct.image_url,
        'product[price]': normalizedProduct.price,
        'product[affiliate_url]': normalizedProduct.affiliate_url,
        'product[source]': normalizedProduct.source,
        'product[ean]': normalizedProduct.ean,
        section_id: sectionId,
    };
    
    fetch('<?= base_url('index.php/dashboard/product/add') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(params)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-check"></i> Toegevoegd';
                    btn.classList.remove('btn-success', 'btn-outline-primary');
                    btn.classList.add('btn-success');
                    btn.disabled = true;
                }
                showToast(`"${product.title}" toegevoegd!`, 'success');
                // Refresh product list without page reload
                refreshProductList();
            } else {
                showToast('Fout: ' + data.message, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        } catch (e) {
            console.error('Invalid JSON response:', text);
            showToast('Fout bij toevoegen product', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fout bij toevoegen product', 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

// Render pagination
function renderPagination() {
    const totalPages = Math.ceil(totalResults / resultsPerPage);
    
    if (totalPages <= 1) {
        document.getElementById('paginationContainer').classList.add('d-none');
        return;
    }
    
    document.getElementById('paginationContainer').classList.remove('d-none');
    
    let html = '';
    
    // Previous button
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    // Next button
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    document.getElementById('pagination').innerHTML = html;
}

// Change page for filtered results
function changePage(page) {
    currentPage = page;
    renderFilteredProducts();
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Refresh product list without page reload
function refreshProductList() {
    fetch('<?= base_url('index.php/dashboard/list/products/' . $list['id']) ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products) {
                updateProductListUI(data.products);
            }
        })
        .catch(error => {
            console.error('Error refreshing product list:', error);
        });
}

// Update product list UI
function updateProductListUI(products) {
    const productListDiv = document.getElementById('productList');
    
    if (products.length === 0) {
        productListDiv.innerHTML = '<p class="text-muted text-center">No products added yet. Search and add products from Bol.com.</p>';
        return;
    }
    
    let html = '';
    products.forEach(product => {
        html += `
            <div class="card mb-3" data-product-id="${product.product_id}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            ${product.image_url ? `<img src="${escapeHtml(product.image_url)}" class="img-fluid" alt="${escapeHtml(product.title)}">` : ''}
                        </div>
                        <div class="col-md-8">
                            <h6>${escapeHtml(product.title)}</h6>
                            <p class="text-muted mb-0">${product.description ? escapeHtml(product.description.substring(0, 100)) : ''}${product.description && product.description.length > 100 ? '...' : ''}</p>
                            ${product.price ? `<strong class="text-primary">€${parseFloat(product.price).toFixed(2)}</strong>` : ''}
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-sm btn-danger" onclick="removeProduct(${product.product_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    productListDiv.innerHTML = html;
}

function removeProduct(productId) {
    if (!confirm('Are you sure you want to remove this product?')) return;

    // Show loading state on the product card
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (productCard) {
        productCard.style.opacity = '0.5';
    }

    fetch('<?= base_url('index.php/dashboard/product/remove') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            list_id: listId,
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product removed successfully', 'success');
            // Refresh product list without page reload
            refreshProductList();
        } else {
            showToast('Error: ' + data.message, 'error');
            if (productCard) {
                productCard.style.opacity = '1';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error removing product', 'error');
        if (productCard) {
            productCard.style.opacity = '1';
        }
    });
}

// Allow Enter key to search
document.getElementById('productSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchProducts();
    }
});

// Initialize drag-and-drop for product reordering
function initializeSortable() {
    const productList = document.getElementById('productList');
    if (!productList) return;
    
    // Remove existing Sortable instance if any
    if (productList.sortableInstance) {
        productList.sortableInstance.destroy();
    }
    
    // Create new Sortable instance
    productList.sortableInstance = Sortable.create(productList, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        handle: '.card-body',
        onEnd: function(evt) {
            // Save new order
            saveProductOrder();
        }
    });
}

// Save product order after drag-and-drop
function saveProductOrder() {
    const productCards = document.querySelectorAll('#productList .card[data-product-id]');
    const positions = {};
    
    productCards.forEach((card, index) => {
        const productId = card.getAttribute('data-product-id');
        positions[productId] = index + 1; // Position starts from 1
    });
    
    fetch('<?= base_url('index.php/dashboard/product/positions') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            list_id: listId,
            positions: JSON.stringify(positions)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product order updated', 'success');
        } else {
            showToast('Error updating order: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating product order', 'error');
    });
}

// Update product list UI with sortable
function updateProductListUI(products) {
    const productListDiv = document.getElementById('productList');
    
    if (products.length === 0) {
        productListDiv.innerHTML = '<p class="text-muted text-center">No products added yet. Search and add products from Bol.com.</p>';
        return;
    }
    
    let html = '';
    products.forEach(product => {
        html += `
            <div class="card mb-3" data-product-id="${product.product_id}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-grip-vertical text-muted" style="cursor: grab;"></i>
                        </div>
                        <div class="col-md-2">
                            ${product.image_url ? `<img src="${escapeHtml(product.image_url)}" class="img-fluid" alt="${escapeHtml(product.title)}">` : ''}
                        </div>
                        <div class="col-md-7">
                            <h6>${escapeHtml(product.title)}</h6>
                            <p class="text-muted mb-0">${product.description ? escapeHtml(product.description.substring(0, 100)) : ''}${product.description && product.description.length > 100 ? '...' : ''}</p>
                            ${product.price ? `<strong class="text-primary">€${parseFloat(product.price).toFixed(2)}</strong>` : ''}
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-sm btn-danger" onclick="removeProduct(${product.product_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    productListDiv.innerHTML = html;
    initializeSortable();
}

// Initialize sortable on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeSortable();
    
    // Handle tab parameter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    if (tabParam === 'details') {
        // Show details tab
        const detailsTab = document.getElementById('details-tab');
        const detailsPane = document.getElementById('details');
        const productsTab = document.getElementById('products-tab');
        const productsPane = document.getElementById('products');
        
        if (detailsTab && detailsPane) {
            detailsTab.classList.add('active');
            detailsPane.classList.add('show', 'active');
            
            productsTab.classList.remove('active');
            productsPane.classList.remove('show', 'active');
        }
    }
});

// ========================================
// SECTION MANAGEMENT FUNCTIONS
// ========================================

// Show add section modal
function showAddSectionModal() {
    const title = prompt('Voer sectie titel in (bijv. Sieraden, Tech, Lifetime Wensen):');
    if (!title || title.trim() === '') {
        return;
    }
    
    addSection(title.trim());
}

// Add new section
function addSection(title) {
    fetch('<?= base_url('index.php/dashboard/section/add') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            list_id: listId,
            title: title
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Sectie "${title}" aangemaakt!`, 'success');
            // Reload page to show new section
            location.reload();
        } else {
            showToast('Fout: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fout bij aanmaken sectie', 'error');
    });
}

// Edit section
function editSection(sectionId, currentTitle) {
    const newTitle = prompt('Wijzig sectie titel:', currentTitle);
    if (!newTitle || newTitle.trim() === '' || newTitle === currentTitle) {
        return;
    }
    
    fetch('<?= base_url('index.php/dashboard/section/update') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            section_id: sectionId,
            title: newTitle.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Sectie bijgewerkt!', 'success');
            // Update UI without reload
            const sectionItem = document.querySelector(`[data-section-id="${sectionId}"]`);
            if (sectionItem) {
                const titleDisplay = sectionItem.querySelector('.section-title-display');
                if (titleDisplay) {
                    titleDisplay.textContent = newTitle;
                }
            }
            // Also update all dropdowns
            document.querySelectorAll('select option').forEach(option => {
                if (option.value == sectionId) {
                    option.textContent = '📁 ' + newTitle;
                }
            });
        } else {
            showToast('Fout: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fout bij bijwerken sectie', 'error');
    });
}

// Delete section
function deleteSection(sectionId) {
    if (!confirm('Weet je zeker dat je deze sectie wilt verwijderen? Producten in deze sectie blijven behouden maar verliezen hun sectie-toewijzing.')) {
        return;
    }
    
    fetch('<?= base_url('index.php/dashboard/section/delete') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            section_id: sectionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Sectie verwijderd!', 'success');
            // Reload page to update UI
            location.reload();
        } else {
            showToast('Fout: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fout bij verwijderen sectie', 'error');
    });
}

// Move product to section
function moveProductToSection(listProductId, sectionId) {
    // Convert empty string to null
    const targetSectionId = sectionId === '' ? null : sectionId;
    
    fetch('<?= base_url('index.php/dashboard/product/move-to-section') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            list_product_id: listProductId,
            section_id: targetSectionId || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product verplaatst!', 'success');
            // Update product count in sections list
            refreshSectionCounts();
        } else {
            showToast('Fout: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fout bij verplaatsen product', 'error');
    });
}

// Refresh section product counts
function refreshSectionCounts() {
    // Count products per section from current page
    const sectionCounts = {};
    document.querySelectorAll('[data-list-product-id]').forEach(productCard => {
        const select = productCard.querySelector('select');
        if (select && select.value) {
            const sectionId = select.value;
            sectionCounts[sectionId] = (sectionCounts[sectionId] || 0) + 1;
        }
    });
    
    // Update badges
    document.querySelectorAll('[data-section-id]').forEach(sectionItem => {
        const sectionId = sectionItem.dataset.sectionId;
        const badge = sectionItem.querySelector('.badge');
        if (badge) {
            const count = sectionCounts[sectionId] || 0;
            badge.textContent = count + ' producten';
        }
    });
}

// ========================================
// GROUP GIFT FUNCTIONS
// ========================================

// Toggle group gift for a product
function toggleGroupGift(listProductId, isEnabled) {
    const amountDiv = document.getElementById('groupGiftAmount' + listProductId);
    const targetInput = document.getElementById('targetAmount' + listProductId);
    
    // Show/hide amount input
    amountDiv.style.display = isEnabled ? 'block' : 'none';
    
    if (!isEnabled) {
        // Disable group gift
        updateGroupGiftStatus(listProductId, false, null);
        return;
    }
    
    // Validate target amount before enabling
    const targetAmount = parseFloat(targetInput.value);
    if (!targetAmount || targetAmount <= 0) {
        showToast('Voer een geldig doelbedrag in', 'warning');
        document.getElementById('groupGift' + listProductId).checked = false;
        amountDiv.style.display = 'none';
        return;
    }
    
    updateGroupGiftStatus(listProductId, true, targetAmount);
}

// Update group gift amount
function updateGroupGiftAmount(listProductId, targetAmount) {
    const checkbox = document.getElementById('groupGift' + listProductId);
    
    if (!checkbox.checked) {
        return; // Not enabled, no need to update
    }
    
    targetAmount = parseFloat(targetAmount);
    if (!targetAmount || targetAmount <= 0) {
        showToast('Voer een geldig doelbedrag in', 'warning');
        return;
    }
    
    updateGroupGiftStatus(listProductId, true, targetAmount);
}

// Send update to server
function updateGroupGiftStatus(listProductId, isGroupGift, targetAmount) {
    fetch('<?= base_url('index.php/contribution/toggle') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            list_product_id: listProductId,
            is_group_gift: isGroupGift ? '1' : '0',
            target_amount: targetAmount || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast('Fout: ' + data.message, 'error');
            // Revert checkbox if failed
            const checkbox = document.getElementById('groupGift' + listProductId);
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fout bij bijwerken groepscadeau', 'error');
    });
}
</script>

<!-- Sortable.js for drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?= $this->endSection() ?>
