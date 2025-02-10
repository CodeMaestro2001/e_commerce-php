<?php
include 'config.php';

// Initialize variables
$success_message = '';
$error_message = '';

// Fetch all active products from the database
$sql = "SELECT * FROM products WHERE active = 1";
$result = mysqli_query($conn, $sql);

// Handle Delete Request (Soft Delete)
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql = "UPDATE products SET active = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: admin_add_product.php?success=deleted");
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

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

// Handle Add/Edit Product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    
    if (isset($_POST['edit_id'])) {
        // Update existing product
        $edit_id = mysqli_real_escape_string($conn, $_POST['edit_id']);
        $sql = "UPDATE products SET title = ?, description = ?, price = ?, stock_quantity = ?, category = ?, size = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssissi", $title, $description, $price, $stock_quantity, $category, $size, $edit_id);
        mysqli_stmt_execute($stmt);
        $product_id = $edit_id;
    } else {
        // Insert new product
        $sql = "INSERT INTO products (title, description, price, stock_quantity, category, size, active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsss", $title, $description, $price, $stock_quantity, $category, $size);
        mysqli_stmt_execute($stmt);
        $product_id = mysqli_insert_id($conn);
    }

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $value) {
            if ($_FILES['images']['error'][$key] === 0) {
                $file = array(
                    'name' => $_FILES['images']['name'][$key],
                    'type' => $_FILES['images']['type'][$key],
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'error' => $_FILES['images']['error'][$key],
                    'size' => $_FILES['images']['size'][$key]
                );
                
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

// Function to get product images
function getProductImages($conn, $product_id) {
    $images = array();
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
    <form action="admin_add_product.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Product Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" step="0.01" class="form-control" name="price" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" name="stock_quantity" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" class="form-control" name="category" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Size</label>
            <input type="text" class="form-control" name="size" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Images</label>
            <input type="file" class="form-control" name="images[]" multiple accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

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
                <th>Size</th>
                <th>Stock</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $product_images = getProductImages($conn, $row['id']);
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['size']); ?></td>
                    <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                    <td>
                        <?php foreach ($product_images as $image): ?>
                            <img src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" width="50" alt="Product Image">
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="admin_add_product.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Product Title</label>
                                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" name="stock_quantity" value="<?php echo htmlspecialchars($row['stock_quantity']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Size</label>
                                        <input type="text" class="form-control" name="size" value="<?php echo htmlspecialchars($row['size']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Current Images</label>
                                        <div class="d-flex gap-2 mb-2">
                                            <?php foreach ($product_images as $image): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" width="50" alt="Product Image">
                                            <?php endforeach; ?>
                                        </div>
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
            <tr><td colspan="9">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submissions
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