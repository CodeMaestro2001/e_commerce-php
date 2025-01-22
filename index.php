<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container mt-4">
    <!-- Bootstrap Carousel -->
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/slide1.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Shop the Latest Trends</h5>
                    <p>Discover new arrivals and exclusive collections.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slide2.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Exclusive Offers</h5>
                    <p>Don't miss out on our limited-time discounts.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slide3.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Fast & Secure Checkout</h5>
                    <p>Shop with confidence and ease.</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<!-- Newly Added Products Section -->
<div class="container mt-5">
    <h2 class="text-center">New Arrivals</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        // Fetch the latest 6 products
        $latest_products_query = "SELECT * FROM products WHERE active = 1 ORDER BY id DESC LIMIT 6";
        $latest_products_result = mysqli_query($conn, $latest_products_query);

        while ($product = mysqli_fetch_assoc($latest_products_result)): ?>
            <div class="col">
                <div class="card h-100 product-card">
                    <img src="uploads/<?php echo htmlspecialchars($product['image1']); ?>" 
                         class="card-img-top product-image" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>">

                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                        <p class="card-text"><strong>Price: $<?php echo number_format($product['price'], 2); ?></strong></p>
                        <form action="products.php" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="10" class="form-control form-control-sm w-25">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
