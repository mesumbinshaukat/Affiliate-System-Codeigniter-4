<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Lijst Bewerken: <?= esc($list['title']) ?></h1>

    <ul class="nav nav-tabs mb-4" id="listTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                <i class="fas fa-info-circle"></i> Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                <i class="fas fa-box"></i> Producten
            </button>
        </li>
    </ul>

    <div class="tab-content" id="listTabsContent">
        <!-- Details Tab -->
        <div class="tab-pane fade show active" id="details" role="tabpanel">
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
        <div class="tab-pane fade" id="products" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Example Lists -->
                    <?php if (!empty($exampleLists)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-lightbulb"></i> Vergelijkbare Lijsten in deze Categorie
                                <?php if ($userAge): ?>
                                    <small class="text-muted">(voor leeftijd <?= $userAge ?>)</small>
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted small">Laat u inspireren door andere lijsten in dezelfde categorie</p>
                            <div class="row">
                                <?php foreach ($exampleLists as $exampleList): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-light">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= esc(character_limiter($exampleList['title'], 40)) ?></h6>
                                                <p class="card-text small text-muted"><?= esc(character_limiter($exampleList['description'], 60)) ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">door <?= esc($exampleList['username']) ?></small>
                                                    <a href="<?= base_url('index.php/list/' . $exampleList['slug']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i> Bekijken
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Search Products -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Producten Toevoegen van Bol.com</h5>
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

// Search products with pagination
function searchProducts(page = 1) {
    const query = document.getElementById('productSearch').value.trim();
    if (!query) {
        showToast('Voer een zoekterm in', 'warning');
        return;
    }

    currentSearchQuery = query;
    currentPage = page;
    const offset = (page - 1) * resultsPerPage;

    // Show loading
    const searchBtn = document.getElementById('searchBtn');
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Zoeken...';
    
    document.getElementById('searchResults').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Laden...</span></div><p class="mt-2 text-muted">Bol.com doorzoeken...</p></div>';

    fetch(`<?= base_url('index.php/dashboard/products/search') ?>?q=${encodeURIComponent(query)}&limit=${resultsPerPage}&offset=${offset}`)
        .then(response => response.json())
        .then(data => {
            searchBtn.disabled = false;
            searchBtn.innerHTML = '<i class="fas fa-search"></i> Zoeken';
            
            if (data.success && data.products && data.products.length > 0) {
                totalResults = data.total || data.products.length;
                renderSearchResults(data.products);
                renderPagination();
            } else {
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
                        <button class="btn btn-sm btn-outline-primary" onclick='addSingleProduct(${JSON.stringify(product)})' title="Add this product">
                            <i class="fas fa-plus"></i> Add Now
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
        // Find product data from current results
        const checkbox = document.getElementById(`product-${productId}`);
        const resultDiv = document.getElementById(`result-${productId}`);
        
        // Extract product data from the result div
        fetch(`<?= base_url('index.php/dashboard/products/search') ?>?q=${encodeURIComponent(currentSearchQuery)}&limit=${resultsPerPage}&offset=${(currentPage - 1) * resultsPerPage}`)
            .then(response => response.json())
            .then(data => {
                const product = data.products.find(p => p.external_id === productId);
                if (product) {
                    selectedProducts.set(productId, product);
                    updateSelectedCounter();
                }
            });
    } else {
        selectedProducts.delete(productId);
        updateSelectedCounter();
    }
}

// Toggle select all - instantly add all products on current page
function toggleSelectAll(isSelected) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    if (isSelected) {
        // Get all products from current search results and add them instantly
        fetch(`<?= base_url('index.php/dashboard/products/search') ?>?q=${encodeURIComponent(currentSearchQuery)}&limit=${resultsPerPage}&offset=${(currentPage - 1) * resultsPerPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.products) {
                    // Add all products to selectedProducts map instantly
                    data.products.forEach(product => {
                        selectedProducts.set(product.external_id, product);
                    });
                    // Check all checkboxes
                    checkboxes.forEach(checkbox => checkbox.checked = true);
                    updateSelectedCounter();
                }
            });
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addedCount++;
            } else {
                failedCount++;
                console.log(`Failed to add product: ${product.title} - ${data.message}`);
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

// Add single product immediately
function addSingleProduct(product) {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check"></i> Added';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            showToast(`Added "${product.title}"`, 'success');
            // Refresh product list without page reload
            refreshProductList();
        } else {
            showToast('Error: ' + data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product', 'error');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
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
            <a class="page-link" href="#" onclick="searchProducts(${currentPage - 1}); return false;">
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
        html += `<li class="page-item"><a class="page-link" href="#" onclick="searchProducts(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="searchProducts(${i}); return false;">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="searchProducts(${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    // Next button
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="searchProducts(${currentPage + 1}); return false;">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    document.getElementById('pagination').innerHTML = html;
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
});
</script>

<!-- Sortable.js for drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?= $this->endSection() ?>
