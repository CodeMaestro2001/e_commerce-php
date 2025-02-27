<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<!-- Hero Section with Carousel -->
<div class="container-fluid p-0">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/slide1.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption">
                    <h1 class="display-4 fw-bold">New Season Arrivals</h1>
                    <p class="lead">Check out our latest collection for this season</p>
                    <a href="products.php" class="btn btn-light btn-lg">Shop Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slide2.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption">
                    <h1 class="display-4 fw-bold">Special Offers</h1>
                    <p class="lead">Up to 50% off on selected items</p>
                    <a href="sales.php" class="btn btn-light btn-lg">View Offers</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slide3.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption">
                    <h1 class="display-4 fw-bold">Premium Quality</h1>
                    <p class="lead">Discover our premium collection</p>
                    <a href="premium.php" class="btn btn-light btn-lg">Explore</a>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<!-- Features Section -->
<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="text-center p-3">
                <i class="bi bi-truck fs-1"></i>
                <h5 class="mt-3">Free Shipping</h5>
                <p class="text-muted">On orders over $100</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3">
                <i class="bi bi-shield-check fs-1"></i>
                <h5 class="mt-3">Secure Payment</h5>
                <p class="text-muted">100% secure payment</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3">
                <i class="bi bi-arrow-counterclockwise fs-1"></i>
                <h5 class="mt-3">Easy Returns</h5>
                <p class="text-muted">14 day return policy</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3">
                <i class="bi bi-headset fs-1"></i>
                <h5 class="mt-3">24/7 Support</h5>
                <p class="text-muted">Dedicated support</p>
            </div>
        </div>
    </div>
</div>

<!-- Latest Products Section -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Latest Products</h2>
        <a href="products.php" class="btn btn-outline-dark">View All</a>
    </div>
    
    <div class="row g-4">
        <?php
        // Fetch latest 9 products with their images
        $sql = "SELECT p.*, 
                GROUP_CONCAT(pi.image_url) AS product_images 
                FROM products p 
                LEFT JOIN product_images pi ON p.id = pi.product_id 
                WHERE p.active = 1 
                GROUP BY p.id 
                ORDER BY p.created_at DESC 
                LIMIT 9";
        
        $result = mysqli_query($conn, $sql);
        
        while ($product = mysqli_fetch_assoc($result)):
            // Get first image from the concatenated image URLs
            $images = explode(',', $product['product_images']);
            $first_image = !empty($images[0]) ? $images[0] : 'default-product.jpg';
        ?>
            <div class="col-md-4">
                <div class="card h-100 product-card border-0 shadow-sm">
                    <div class="position-relative">
                        <a href="product-details.php?id=<?php echo $product['id']; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($first_image); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>"
                                 style="height: 300px; object-fit: cover;">
                        </a>
                        <?php if ($product['stock_quantity'] <= 0): ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-danger">Out of Stock</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" 
                               class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($product['title']); ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted">
                            <?php echo substr(htmlspecialchars($product['description']), 0, 100) . '...'; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">$<?php echo number_format($product['price'], 2); ?></h5>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <form action="cart.php" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="bi bi-cart-x"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Categories Section (Using category_id) -->
<div class="container my-5">
    <h2 class="fw-bold mb-4 text-center">Shop by Category</h2>
    
    <div class="row g-4 justify-content-center">
        <?php
        // Map category_id to category name and image
        $category_data = [
            1 => [
                'name'  => 'Mens Wear',
                'image' => 'category_mens.png' // e.g. images/category_mens.png
            ],
            2 => [
                'name'  => 'Ladies Wear',
                'image' => 'images/ladies.jpg'
            ],
            3 => [
                'name'  => 'Kids Wear',
                'image' => 'category_kids.png'
            ],
            4 => [
                'name'  => 'Intimate Apparel',
                'image' => 'category_intimate.png'
            ],
        ];

        // Fetch distinct category_id from products
        $categories_query = "SELECT DISTINCT category_id 
                             FROM products 
                             WHERE active = 1 
                               AND category_id IN (1,2,3,4) 
                             LIMIT 4";
        $categories_result = mysqli_query($conn, $categories_query);

        // Loop through the category rows returned from the DB
        while ($cat = mysqli_fetch_assoc($categories_result)):
            $cat_id = (int)$cat['category_id'];

            // If not in our $category_data map, skip
            if (!isset($category_data[$cat_id])) {
                continue;
            }

            $cat_name  = $category_data[$cat_id]['name'];
            $cat_image = $category_data[$cat_id]['image'];
            ?>
            
            <div class="col-6 col-md-3 text-center">
                <a href="products.php?category_id=<?php echo urlencode($cat_id); ?>" 
                   class="text-decoration-none text-dark">
                    
                    <!-- Circle for the category icon -->
                    <div class="category-circle mx-auto mb-3">
                        <img src="images/<?php echo htmlspecialchars($cat_image); ?>" 
                             alt="<?php echo htmlspecialchars($cat_name); ?>">
                    </div>
                    
                    <!-- Category Name -->
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($cat_name); ?></h5>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>


<!-- Newsletter Section -->
<div class="container-fluid bg-light py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h3 class="fw-bold">Subscribe to Our Newsletter</h3>
                <p class="text-muted">Stay updated with our latest products and offers</p>
                <form class="d-flex gap-2">
                    <input type="email" class="form-control" placeholder="Enter your email">
                    <button type="submit" class="btn btn-dark">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.carousel-item img {
    height: 70vh;
    object-fit: cover;
}

.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    padding: 2rem;
    border-radius: 10px;
}

.product-card {
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.category-card {
    transition: transform 0.3s ease;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
}

.category-card:hover {
    transform: translateY(-5px);
}

@media (max-width: 768px) {
    .carousel-item img {
        height: 50vh;
    }
    
    .carousel-caption {
        padding: 1rem;
    }
    
    .carousel-caption h1 {
        font-size: 1.5rem;
    }
}

.category-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-circle img {
    width: 100%;
    height: 100%;
    object-fit: contain; 
    /* or 'cover', if you want the image to fill the circle more aggressively */
}

</style>

<?php include 'footer.php'; ?>
