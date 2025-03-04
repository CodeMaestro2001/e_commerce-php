<?php
// Start session at the very beginning
session_start();

// Require the main functions file with database connection
require_once 'config.php';
require_once 'main_functions.php';

// Create a new Auth instance
$auth = new Auth($conn);

// DOUBLE SECURITY - Check again if user is admin
if (!$auth->isAdmin()) {
    $_SESSION['error'] = "Access Denied. Administrator privileges required.";
    header("Location: login.php");
    exit();
}

// Continue only if admin check passed
?>



<?php
include 'config.php';

// -----------------------------------------
// Utility Functions
// -----------------------------------------

// Function to handle image upload
function handleImageUpload($file) {
    if (!empty($file['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($file['name']);
        $target_path = $target_dir . $image_name;
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return $image_name;
        }
    }
    return null;
}

// Function to get product images
function getProductImages($conn, $product_id) {
    $images = [];
    $sql = "SELECT * FROM product_images WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
}

// Function to get stock for a product (returns an associative array: [ 'UK6' => qty, 'UK8' => qty, ... ])
function getStockForProduct($conn, $product_id) {
    $stock_data = [
        'UK6'  => 0,
        'UK8'  => 0,
        'UK10' => 0,
        'UK12' => 0,
        'UK14' => 0,
        'UK16' => 0
    ];
    $sql = "SELECT size, stock_quantity FROM product_stock WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $stock_data[$row['size']] = $row['stock_quantity'];
    }
    return $stock_data;
}

// -----------------------------------------
// Handle Delete (Soft Delete)
// -----------------------------------------
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql = "UPDATE products SET active = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: admin_add_product.php?success=deleted");
        exit();
    } else {
        echo "Error deleting product: " . mysqli_error($conn);
    }
}

// -----------------------------------------
// Handle Add/Edit Product
// -----------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $price         = floatval($_POST['price']);
    $category_id   = intval($_POST['category_id']);
    // We no longer use single stock_quantity from products table
    // Instead we handle multiple sizes in $_POST['stock']

    // Decide if this is ADD or EDIT
    if (isset($_POST['edit_id'])) {
        // -------------------------------------
        // Update existing product
        // -------------------------------------
        $edit_id = intval($_POST['edit_id']);

        $sql = "UPDATE products 
                SET title = ?, description = ?, price = ?, category_id = ?, updated_at = NOW() 
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdii", $title, $description, $price, $category_id, $edit_id);
        mysqli_stmt_execute($stmt);

        // Update stock for each size
        foreach ($_POST['stock'] as $size => $quantity) {
            $quantity = intval($quantity);
            $sql = "UPDATE product_stock SET stock_quantity = ? 
                    WHERE product_id = ? AND size = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iis", $quantity, $edit_id, $size);
            mysqli_stmt_execute($stmt);
        }

        // Handle image uploads (if any)
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $value) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $file = [
                        'name'     => $_FILES['images']['name'][$key],
                        'type'     => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error'    => $_FILES['images']['error'][$key],
                        'size'     => $_FILES['images']['size'][$key]
                    ];

                    $image_name = handleImageUpload($file);
                    if ($image_name) {
                        $sql = "INSERT INTO product_images (product_id, image_url) VALUES (?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "is", $edit_id, $image_name);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }
        }

        header("Location: admin_add_product.php?success=updated");
        exit();

    } else {
        // -------------------------------------
        // Insert new product
        // -------------------------------------
        $sql = "INSERT INTO products (title, description, price, category_id, active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdi", $title, $description, $price, $category_id);
        mysqli_stmt_execute($stmt);
        $product_id = mysqli_insert_id($conn);

        // Insert stock for each size
        foreach ($_POST['stock'] as $size => $quantity) {
            $quantity = intval($quantity);
            $sql = "INSERT INTO product_stock (product_id, size, stock_quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isi", $product_id, $size, $quantity);
            mysqli_stmt_execute($stmt);
        }

        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $value) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $file = [
                        'name'     => $_FILES['images']['name'][$key],
                        'type'     => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error'    => $_FILES['images']['error'][$key],
                        'size'     => $_FILES['images']['size'][$key]
                    ];

                    $image_name = handleImageUpload($file);
                    if ($image_name) {
                        $sql = "INSERT INTO product_images (product_id, image_url) VALUES (?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "is", $product_id, $image_name);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }
        }

        header("Location: admin_add_product.php?success=added");
        exit();
    }
}

// -----------------------------------------
// Fetch Active Products
// -----------------------------------------
$sql = "SELECT * FROM products WHERE active = 1 ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Add Product</h1>
    <!-- ADD PRODUCT FORM -->
    <form action="admin_add_product.php" method="POST" enctype="multipart/form-data">
        <!-- Title -->
        <div class="mb-3">
            <label class="form-label">Product Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" required></textarea>
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" step="0.01" class="form-control" name="price" required>
        </div>

        <!-- Category ID Field (Dropdown) -->
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <option value="1">Mens Wear</option>
                <option value="2">Ladies Wear</option>
                <option value="3">Kids Wear</option>
                <option value="4">Intimate Apparel</option>
            </select>
        </div>

        <!-- Stock for Each Size -->
        <div class="mb-3">
            <label class="form-label">Stock for Sizes</label>
            <div class="row">
                <div class="col">
                    <label>UK6</label>
                    <input type="number" class="form-control" name="stock[UK6]" required>
                </div>
                <div class="col">
                    <label>UK8</label>
                    <input type="number" class="form-control" name="stock[UK8]" required>
                </div>
                <div class="col">
                    <label>UK10</label>
                    <input type="number" class="form-control" name="stock[UK10]" required>
                </div>
                <div class="col">
                    <label>UK12</label>
                    <input type="number" class="form-control" name="stock[UK12]" required>
                </div>
                <div class="col">
                    <label>UK14</label>
                    <input type="number" class="form-control" name="stock[UK14]" required>
                </div>
                <div class="col">
                    <label>UK16</label>
                    <input type="number" class="form-control" name="stock[UK16]" required>
                </div>
            </div>
        </div>

        <!-- Upload Images -->
        <div class="mb-3">
            <label class="form-label">Upload Images</label>
            <input type="file" class="form-control" name="images[]" multiple accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<!-- PRODUCT LIST -->
<div class="container mt-5">
    <h1>Product List</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock (by Size)</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php 
                // Get images
                $product_images = getProductImages($conn, $row['id']);
                // Get stock data
                $stock_data = getStockForProduct($conn, $row['id']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                    
                    <!-- Display stock by size -->
                    <td>
                        <?php
                        // You can loop through an array of sizes here:
                        $sizes = ['UK6','UK8','UK10','UK12','UK14','UK16'];
                        foreach ($sizes as $size) {
                            echo "<strong>$size:</strong> " . intval($stock_data[$size]) . "<br>";
                        }
                        ?>
                    </td>

                    <!-- Display images -->
                    <td>
                        <?php foreach ($product_images as $image): ?>
                            <img src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" width="50" alt="Product Image">
                        <?php endforeach; ?>
                    </td>

                    <!-- Actions -->
                    <td>
                        <!-- Edit Button -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>

                        <!-- Delete Button -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
                    </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="admin_add_product.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Product (ID: <?php echo $row['id']; ?>)</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">

                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label class="form-label">Product Title</label>
                                        <input type="text" class="form-control" name="title"
                                               value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-3">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" step="0.01" class="form-control" name="price"
                                               value="<?php echo htmlspecialchars($row['price']); ?>" required>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="1" <?php if($row['category_id'] == 1) echo 'selected'; ?>>Mens Wear</option>
                                            <option value="2" <?php if($row['category_id'] == 2) echo 'selected'; ?>>Ladies Wear</option>
                                            <option value="3" <?php if($row['category_id'] == 3) echo 'selected'; ?>>Kids Wear</option>
                                            <option value="4" <?php if($row['category_id'] == 4) echo 'selected'; ?>>Intimate Apparel</option>
                                        </select>
                                    </div>

                                    <!-- Stock for Each Size -->
                                    <div class="mb-3">
                                        <label class="form-label">Stock for Sizes</label>
                                        <div class="row">
                                            <?php
                                            $current_stock = getStockForProduct($conn, $row['id']);
                                            $sizes = ['UK6','UK8','UK10','UK12','UK14','UK16'];
                                            foreach ($sizes as $size) {
                                                ?>
                                                <div class="col">
                                                    <label><?php echo $size; ?></label>
                                                    <input type="number" class="form-control" name="stock[<?php echo $size; ?>]"
                                                           value="<?php echo intval($current_stock[$size]); ?>" required>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- Current Images & Upload More -->
                                    <div class="mb-3">
                                        <label class="form-label">Current Images</label>
                                        <div class="d-flex gap-2 mb-2">
                                            <?php foreach ($product_images as $image): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" width="50" alt="Product Image">
                                            <?php endforeach; ?>
                                        </div>
                                        <label class="form-label">Add More Images</label>
                                        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Disable submit button on form submission (prevent double click)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            }
        });
    });

    // Handle delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = `admin_add_product.php?delete_id=${id}`;
            }
        });
    });
});
</script>

</body>
</html>
