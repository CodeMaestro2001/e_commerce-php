<?php
// products.php

include 'config.php'; // your DB connection, etc.
// include 'header.php'; // (Optional) if you have a separate header file

// --------------------------------------------
// 1. Capture Filter Inputs (Category & Size)
// --------------------------------------------
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$selectedSize     = isset($_GET['size'])     ? trim($_GET['size'])     : '';

// --------------------------------------------
// 2. Build the SQL Query with Optional Filters
// --------------------------------------------
// We will join product_stock so we can filter by size (and check stock > 0).
// If you prefer to show out-of-stock items, remove "AND ps.stock_quantity > 0".

$sql = "
  SELECT DISTINCT p.id, p.title, p.description, p.price, p.category_id
  FROM products p
  LEFT JOIN product_stock ps ON p.id = ps.product_id
  WHERE p.active = 1
";

// Filter by category if set
if (!empty($selectedCategory)) {
    $sql .= " AND p.category_id = ? ";
}

// Filter by size if set
if (!empty($selectedSize)) {
    $sql .= " AND ps.size = ? AND ps.stock_quantity > 0 ";
}

// Order by newest or any criteria you like
$sql .= " ORDER BY p.id DESC";

// --------------------------------------------
// 3. Prepare & Bind the Statement
// --------------------------------------------
$stmt = mysqli_prepare($conn, $sql);

if (!empty($selectedCategory) && !empty($selectedSize)) {
    // Both category and size
    mysqli_stmt_bind_param($stmt, "is", $selectedCategory, $selectedSize);
} elseif (!empty($selectedCategory)) {
    // Only category
    mysqli_stmt_bind_param($stmt, "i", $selectedCategory);
} elseif (!empty($selectedSize)) {
    // Only size
    mysqli_stmt_bind_param($stmt, "s", $selectedSize);
}

// --------------------------------------------
// 4. Execute & Fetch Products
// --------------------------------------------
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt);

// --------------------------------------------
// 5. Helper: Get Product's Images
// --------------------------------------------
function getProductImages($conn, $product_id) {
    $sql = "SELECT image_url FROM product_images WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $images = [];
    while ($img = mysqli_fetch_assoc($res)) {
        $images[] = $img['image_url'];
    }
    return $images;
}

// --------------------------------------------
// 6. Helper: Map Category IDs to Names
// --------------------------------------------
// You may have your own categories. Adjust accordingly.
// Or, if you have a categories table, fetch them dynamically.
function getCategoryName($catId) {
    switch ($catId) {
        case 1: return 'Dresses';
        case 2: return 'Tops';
        case 3: return 'Jeans';
        case 4: return 'Trousers';
        case 5: return 'Skirts';
        case 6: return 'Shorts';
        // etc. You can expand as you like
        default: return 'Other';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Products</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <style>
    /* Quick side filter + product grid layout */
    .page-container {
      display: flex;
      gap: 2rem;
    }
    .filter-sidebar {
      flex: 0 0 250px;
    }
    .product-list {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1rem;
    }
    .product-card {
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 10px;
      text-align: center;
      transition: box-shadow 0.2s;
    }
    .product-card:hover {
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }
    .product-card img {
      max-width: 100%;
      height: auto;
      object-fit: cover;
    }
    .product-card h5 {
      margin: 0.5rem 0;
      font-size: 1.1rem;
    }
    .product-card p {
      font-size: 0.9rem;
      color: #555;
    }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="page-container">
    <!-- ====================================== -->
    <!--  LEFT SIDEBAR: Categories & Sizes      -->
    <!-- ====================================== -->
    <div class="filter-sidebar">
      <h5>Categories</h5>
      <ul class="list-unstyled">
        <!-- Example static links; adapt to your DB as needed -->
        <li><a href="products.php">All</a></li>
        <li><a href="products.php?category=1">Dresses</a></li>
        <li><a href="products.php?category=2">Tops</a></li>
        <li><a href="products.php?category=3">Jeans</a></li>
        <li><a href="products.php?category=4">Trousers</a></li>
        <li><a href="products.php?category=5">Skirts</a></li>
        <li><a href="products.php?category=6">Shorts</a></li>
        <!-- Add more as needed -->
      </ul>

      <h5>Size</h5>
      <ul class="list-unstyled">
        <!-- Linking to e.g. ?size=UK6, etc. -->
        <li><a href="products.php?size=UK6">6</a></li>
        <li><a href="products.php?size=UK8">8</a></li>
        <li><a href="products.php?size=UK10">10</a></li>
        <li><a href="products.php?size=UK12">12</a></li>
        <li><a href="products.php?size=UK14">14</a></li>
        <li><a href="products.php?size=UK16">16</a></li>
      </ul>

      <!-- If you want combined filters, the user can manually 
           add both, e.g. products.php?category=1&size=UK10 -->
    </div>

    <!-- ====================================== -->
    <!--  MAIN CONTENT: Product Grid           -->
    <!-- ====================================== -->
    <div class="product-list">
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
          <?php 
             // Get up to 1 or more images
             $images = getProductImages($conn, $product['id']);
             $imgSrc = (!empty($images)) 
                       ? 'uploads/' . $images[0] 
                       : 'placeholder.jpg'; // fallback image
          ?>
          <div class="product-card">
            <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Product Image">
            <h5><?php echo htmlspecialchars($product['title']); ?></h5>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
            <p class="text-muted">
              Category: <?php echo getCategoryName($product['category_id']); ?>
            </p>
            <!-- A link to product detail page, if you have one: -->
            <a class="btn btn-sm btn-primary" href="product_detail.php?id=<?php echo $product['id']; ?>">
              View Details
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products found for your filter.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Optional: include 'footer.php' here if you have a separate footer -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
