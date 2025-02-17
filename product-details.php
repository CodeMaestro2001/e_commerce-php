<?php 
include 'header.php';
include 'config.php';

// Simple category map
$category_map = [
    1 => 'Mens Wear',
    2 => 'Ladies Wear',
    3 => 'Kids Wear',
    4 => 'Intimate Apparel'
];

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details (with category_id)
$sql = "SELECT p.*, GROUP_CONCAT(pi.image_url) AS product_images
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE p.id = ? AND p.active = 1
        GROUP BY p.id";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: index.php");
    exit();
}

// Convert category_id to human-readable name
$category_id = (int)$product['category_id'];
$category_name = isset($category_map[$category_id]) ? $category_map[$category_id] : 'Uncategorized';

// Get product images array
$product_images = explode(',', $product['product_images']);

// Fetch related products by category_id, excluding current product
$sql = "SELECT p.*, pi.image_url
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE p.category_id = ? 
          AND p.id != ? 
          AND p.active = 1
        GROUP BY p.id
        LIMIT 4";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $category_id, $product_id);
mysqli_stmt_execute($stmt);
$related_products = mysqli_stmt_get_result($stmt);

// Prepare WhatsApp share link (URL-encoded message)
$whatsapp_message = urlencode(
    "Check out this product: " 
    . $product['title'] 
    . " for ₹" 
    . number_format($product['price'], 2) 
    . "!\n\nBuy here: https://yourdomain.com/product-details.php?id=" 
    . $product['id']
);
$whatsapp_share_url = "https://api.whatsapp.com/send?text={$whatsapp_message}";
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Product Images Section -->
        <div class="col-md-6 mb-4">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img src="uploads/<?php echo htmlspecialchars($product_images[0]); ?>" 
                         class="img-fluid rounded main-product-image" 
                         id="mainImage"
                         alt="<?php echo htmlspecialchars($product['title']); ?>">
                </div>
                
                <!-- Thumbnail Images -->
                <div class="d-flex gap-2 thumbnail-container">
                    <?php foreach ($product_images as $index => $image): ?>
                    <div class="thumbnail" onclick="changeMainImage(this)">
                        <img src="uploads/<?php echo htmlspecialchars($image); ?>" 
                             class="img-fluid rounded <?php echo $index === 0 ? 'active' : ''; ?>"
                             alt="Thumbnail <?php echo $index + 1; ?>"
                             data-image="uploads/<?php echo htmlspecialchars($image); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Product Details Section -->
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <!-- Link to products page filtered by category_id -->
                        <a href="products.php?category_id=<?php echo urlencode($category_id); ?>">
                            <?php echo htmlspecialchars($category_name); ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($product['title']); ?>
                    </li>
                </ol>
            </nav>

            <h1 class="product-title mb-3">
                <?php echo htmlspecialchars($product['title']); ?>
            </h1>
            
            <!-- Reviews (Static) -->
            <div class="mb-3">
                <div class="ratings">
                    <i class="bi bi-star"></i>
                    <i class="bi bi-star"></i>
                    <i class="bi bi-star"></i>
                    <i class="bi bi-star"></i>
                    <i class="bi bi-star"></i>
                    <span class="ms-2">No Reviews Yet</span>
                </div>
            </div>

            <!-- Price -->
            <div class="product-price mb-4">
                <h3>₹<?php echo number_format($product['price'], 2); ?></h3>
                <?php if ($product['size']): ?>
                    <div class="text-muted">From <?php echo htmlspecialchars($product['size']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Size Selection (Static Example) -->
            <div class="mb-4">
                <label class="form-label">Select Size</label>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-dark size-btn">Small</button>
                    <button class="btn btn-outline-dark size-btn">Medium</button>
                    <button class="btn btn-outline-dark size-btn">Large</button>
                    <button class="btn btn-outline-dark size-btn">XL</button>
                </div>
                <a href="#" class="size-chart-link ms-2">Size Chart</a>
            </div>

            <!-- Quantity and Add to Cart -->
            <form action="cart.php" method="POST" class="mb-4">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Quantity</label>
                        <div class="input-group" style="width: 130px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity()">-</button>
                            <input type="number" class="form-control text-center" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo $product['stock_quantity']; ?>" 
                                   id="quantityInput">
                            <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity()">+</button>
                        </div>
                    </div>
                </div>

                <?php if ($product['stock_quantity'] > 0): ?>
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg mt-3">Add to Cart</button>
                <?php else: ?>
                    <button type="button" class="btn btn-danger btn-lg mt-3" disabled>Out of Stock</button>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-dark btn-lg mt-3" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                    <i class="bi bi-heart"></i> Add to Wishlist
                </button>
            </form>

            <!-- WhatsApp Share Button -->
            <div class="mb-4">
                <a href="<?php echo $whatsapp_share_url; ?>" 
                   target="_blank" 
                   class="btn btn-success btn-lg">
                    <i class="bi bi-whatsapp"></i> Share on WhatsApp
                </a>
            </div>

            <!-- Product Description -->
            <div class="product-description mt-4">
                <h4>Product Description</h4>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    <div class="related-products mt-5">
        <h3 class="mb-4">You May Try These Instead...</h3>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <?php while ($related = mysqli_fetch_assoc($related_products)): ?>
                <div class="col">
                    <div class="card h-100 border-0 product-card">
                        <a href="product-details.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                            <?php 
                            // If no image_url, fallback to a default
                            $rel_image = $related['image_url'] ?: 'default-product.jpg';
                            ?>
                            <img src="uploads/<?php echo htmlspecialchars($rel_image); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><?php echo htmlspecialchars($related['title']); ?></h5>
                                <p class="card-text">
                                    <strong>₹<?php echo number_format($related['price'], 2); ?></strong>
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
.product-gallery .main-image {
    width: 100%;
    height: 600px;
    overflow: hidden;
    border-radius: 8px;
}

.product-gallery .main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-container {
    overflow-x: auto;
    padding: 10px 0;
}

.thumbnail {
    width: 80px;
    height: 80px;
    cursor: pointer;
    border-radius: 4px;
    overflow: hidden;
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s;
}

.thumbnail img.active {
    border: 2px solid #0d6efd;
}

.size-btn {
    min-width: 80px;
}

.size-chart-link {
    color: #0d6efd;
    text-decoration: none;
    font-size: 0.9rem;
}

.product-card {
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.ratings {
    color: #ffc107;
}

@media (max-width: 768px) {
    .product-gallery .main-image {
        height: 400px;
    }
}
</style>

<script>
function changeMainImage(element) {
    const newImageSrc = element.querySelector('img').dataset.image;
    document.getElementById('mainImage').src = newImageSrc;
    
    // Update active state
    document.querySelectorAll('.thumbnail img').forEach(img => {
        img.classList.remove('active');
    });
    element.querySelector('img').classList.add('active');
}

function incrementQuantity() {
    const input = document.getElementById('quantityInput');
    const max = parseInt(input.max);
    const currentValue = parseInt(input.value);
    if (currentValue < max) {
        input.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantityInput');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

function addToWishlist(productId) {
    // Add your wishlist functionality here
    alert('Product added to wishlist!');
}

// Initialize size buttons
document.querySelectorAll('.size-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('btn-dark');
            btn.classList.add('btn-outline-dark');
        });
        this.classList.remove('btn-outline-dark');
        this.classList.add('btn-dark');
    });
});
</script>

<?php include 'footer.php'; ?>
