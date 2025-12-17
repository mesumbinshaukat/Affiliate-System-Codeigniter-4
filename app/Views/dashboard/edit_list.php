<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    .personalized-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 18px;
    }

    .personalized-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e6e9ef;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        min-height: 320px;
        padding-bottom: 50px;
    }

    .personalized-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 24px rgba(15, 23, 42, 0.12);
    }

    .personalized-card__media {
        background: linear-gradient(135deg, #f5f8ff, #eef2ff);
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .personalized-card__media img {
        max-height: 140px;
        width: 100%;
        object-fit: contain;
    }

    .personalized-card__body {
        padding: 16px 18px 20px;
        display: flex;
        flex-direction: column;
        height: calc(100% - 150px);
    }

    .personalized-card__title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #1e293b;
        margin-bottom: 8px;
        min-height: 48px;
    }

    .personalized-card__description {
        font-size: 0.85rem;
        color: #64748b;
        flex-grow: 1;
        margin-bottom: 12px;
    }

    .personalized-card__price {
        font-weight: 700;
        color: #0ea5e9;
        font-size: 1rem;
        margin-bottom: 12px;
    }

    .personalized-card__actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .personalized-card__add {
        border: none;
        border-radius: 12px;
        padding: 8px 14px;
        background: #0ea5e9;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        transition: background 0.2s ease;
    }

    .personalized-card__add:hover {
        background: #0284c7;
        color: #fff;
    }

    .personalized-card__pill {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 999px;
        background: #ecfeff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    @media (max-width: 768px) {
        .personalized-card {
            min-height: auto;
        }

        .personalized-card__title {
            min-height: auto;
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
                                <div class="personalized-grid">
                                    <?php foreach (array_slice($personalizedSuggestions, 0, 8) as $product): ?>
                                        <div class="personalized-card">
                                            <div class="personalized-card__media">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="<?= esc($product['image']) ?>" alt="<?= esc($product['title']) ?>">
                                                <?php else: ?>
                                                    <div class="text-muted small">Geen afbeelding</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="personalized-card__body">
                                                <span class="personalized-card__pill">
                                                    <?= esc($product['category'] ?? 'Trending selectie') ?>
                                                </span>
                                                <h6 class="personalized-card__title mt-2"><?= esc(character_limiter($product['title'], 60)) ?></h6>
                                                <p class="personalized-card__description"><?= esc(character_limiter($product['description'] ?? 'Aanbevolen voor jouw leeftijdsgroep.', 95)) ?></p>
                                                <?php if (!empty($product['price'])): ?>
                                                    <div class="personalized-card__price">€<?= number_format($product['price'], 2) ?></div>
                                                <?php endif; ?>
                                                <div class="personalized-card__actions">
                                                    <button class="personalized-card__add" onclick="addSingleProduct(<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>)">
                                                        <i class="fas fa-plus"></i> Toevoegen
                                                    </button>
                                                    <a href="<?= esc($product['url'] ?? '#') ?>" target="_blank" rel="noopener" class="text-muted small">
                                                        Bekijk
                                                    </a>
                                                </div>
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
                                        <div class="card mb-3" data-product-id="<?= $product['product_id'] ?>">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2">
                                                        <?php if ($product['image_url']): ?>
                                                            <img src="<?= esc($product['image_url']) ?>" class="img-fluid" alt="<?= esc($product['title']) ?>">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h6><?= esc($product['title']) ?></h6>
                                                        <p class="text-muted mb-0"><?= esc(character_limiter($product['description'], 100)) ?></p>
                                                        <?php if ($product['price']): ?>
                                                            <strong class="text-primary">€<?= number_format($product['price'], 2) ?></strong>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-2 text-end">
                                                        <button class="btn btn-sm btn-danger" onclick="removeProduct(<?= $product['product_id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center">Nog geen producten toegevoegd. Zoeken en voeg producten van Bol.com toe.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
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
}

// Search products with pagination and filters
function searchProducts(page = 1) {
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
    let html = '<div class="list-group">';
    
    products.forEach(product => {
        const isSelected = selectedProducts.has(product.external_id);
        const checkboxId = `product-${product.external_id}`;
        
        html += `
            <div class="list-group-item" id="result-${product.external_id}">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input product-checkbox" type="checkbox" 
                                   id="${checkboxId}" 
                                   ${isSelected ? 'checked' : ''}
                                   onchange="toggleProductSelection('${product.external_id}', this.checked)">
                            <label class="form-check-label" for="${checkboxId}"></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        ${product.image_url ? `<img src="${escapeHtml(product.image_url)}" class="img-fluid rounded" alt="${escapeHtml(product.title)}" style="max-height: 80px; object-fit: contain;">` : '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;"><i class="fas fa-image text-muted"></i></div>'}
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-1">${escapeHtml(product.title)}</h6>
                        ${product.description ? `<p class="text-muted small mb-1">${escapeHtml(product.description.substring(0, 120))}${product.description.length > 120 ? '...' : ''}</p>` : ''}
                        <div class="d-flex align-items-center gap-3">
                            ${product.price ? `<strong class="text-primary">€${parseFloat(product.price).toFixed(2)}</strong>` : ''}
                            ${product.rating ? `<span class="badge bg-warning text-dark"><i class="fas fa-star"></i> ${product.rating}</span>` : ''}
                            ${product.ean ? `<small class="text-muted">EAN: ${escapeHtml(product.ean)}</small>` : ''}
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-sm btn-outline-primary"
                                data-product="${encodeURIComponent(JSON.stringify(product))}"
                                onclick="addSingleProductFromButton(this)"
                                title="Add this product">
                            <i class="fas fa-plus"></i> Voeg nu toe
                        </button>
                    </div>
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
                'product[ean]': product.ean || ''
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
        source: product.source || 'bol.com',
        ean: product.ean || '',
    };
    
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
</script>

<!-- Sortable.js for drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?= $this->endSection() ?>
