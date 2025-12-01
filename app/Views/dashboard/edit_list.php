<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <h1 class="mb-4">Edit List: <?= esc($list['title']) ?></h1>

    <ul class="nav nav-tabs mb-4" id="listTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                <i class="fas fa-info-circle"></i> Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                <i class="fas fa-box"></i> Products
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
                            <form method="post" action="<?= base_url('dashboard/list/edit/' . $list['id']) ?>">
                                <div class="mb-3">
                                    <label for="title" class="form-label">List Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?= esc($list['title']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Select a category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" <?= $list['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                <?= esc($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= esc($list['description']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft" <?= $list['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="published" <?= $list['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                        <option value="private" <?= $list['status'] === 'private' ? 'selected' : '' ?>>Private</option>
                                    </select>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Update List</button>
                                    <a href="<?= base_url('dashboard/lists') ?>" class="btn btn-secondary">Back to Lists</a>
                                    <?php if ($list['status'] === 'published'): ?>
                                        <a href="<?= base_url('list/' . $list['slug']) ?>" class="btn btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> View Public
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
                    <!-- Search Products -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Add Products from Bol.com</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" id="productSearch" placeholder="Search for products...">
                                <button class="btn btn-primary" type="button" onclick="searchProducts()">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                            <div id="searchResults" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Current Products -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Products in this List</h5>
                            <div id="productList">
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
                                    <p class="text-muted text-center">No products added yet. Search and add products from Bol.com.</p>
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

function searchProducts() {
    const query = document.getElementById('productSearch').value;
    if (!query) return;

    document.getElementById('searchResults').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';

    fetch('<?= base_url('dashboard/products/search') ?>?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                let html = '<div class="list-group">';
                data.products.forEach(product => {
                    html += `
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    ${product.image_url ? `<img src="${product.image_url}" class="img-fluid" alt="${product.title}">` : ''}
                                </div>
                                <div class="col-md-8">
                                    <h6>${product.title}</h6>
                                    <p class="text-muted mb-0">${product.description ? product.description.substring(0, 100) : ''}</p>
                                    ${product.price ? `<strong class="text-primary">€${parseFloat(product.price).toFixed(2)}</strong>` : ''}
                                </div>
                                <div class="col-md-2 text-end">
                                    <button class="btn btn-sm btn-primary" onclick='addProduct(${JSON.stringify(product)})'>
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                document.getElementById('searchResults').innerHTML = html;
            } else {
                document.getElementById('searchResults').innerHTML = '<div class="alert alert-info">No products found. Try a different search term.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('searchResults').innerHTML = '<div class="alert alert-danger">Error searching products. Please try again.</div>';
        });
}

function addProduct(product) {
    fetch('<?= base_url('dashboard/product/add') ?>', {
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
            alert('Product added successfully!');
            location.reload();
        } else {
            alert('Error adding product: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding product');
    });
}

function removeProduct(productId) {
    if (!confirm('Are you sure you want to remove this product?')) return;

    fetch('<?= base_url('dashboard/product/remove') ?>', {
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
            location.reload();
        } else {
            alert('Error removing product: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing product');
    });
}

// Allow Enter key to search
document.getElementById('productSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchProducts();
    }
});
</script>
<?= $this->endSection() ?>
