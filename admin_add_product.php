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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']); // Convert price to float
    
    // Function to handle image upload
    function handleImageUpload($file, $current_image = '') {
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
        return $current_image;
    }

    // Handle Edit Request
    if (isset($_POST['edit_id'])) {
        $edit_id = mysqli_real_escape_string($conn, $_POST['edit_id']);
        
        // Handle image uploads
        $image1 = handleImageUpload($_FILES['image1'], $_POST['current_image1']);
        $image2 = handleImageUpload($_FILES['image2'], $_POST['current_image2']);
        $image3 = handleImageUpload($_FILES['image3'], $_POST['current_image3']);

        // Update query using prepared statement
        $sql = "UPDATE products SET title = ?, description = ?, image1 = ?, image2 = ?, image3 = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssdi", $title, $description, $image1, $image2, $image3, $price, $edit_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_add_product.php?success=updated");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    // Handle Add New Product
    else {
        // Handle image uploads for new product
        $image1 = handleImageUpload($_FILES['image1']);
        $image2 = handleImageUpload($_FILES['image2']);
        $image3 = handleImageUpload($_FILES['image3']);

        // Insert query using prepared statement
        $sql = "INSERT INTO products (title, description, image1, image2, image3, price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssd", $title, $description, $image1, $image2, $image3, $price);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_add_product.php?success=added");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle success messages
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $success_message = "Product added successfully!";
            break;
        case 'updated':
            $success_message = "Product updated successfully!";
            break;
        case 'deleted':
            $success_message = "Product deleted successfully!";
            break;
    }
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
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h1>Add Product</h1>
    <form action="admin_add_product.php" method="POST" enctype="multipart/form-data" id="addProductForm">
        <div class="mb-3">
            <label for="title" class="form-label">Product Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Product Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Product Price ($)</label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
        </div>
        <div class="mb-3">
            <label for="image1" class="form-label">Image 1</label>
            <input type="file" class="form-control" id="image1" name="image1" required accept="image/*">
        </div>
        <div class="mb-3">
            <label for="image2" class="form-label">Image 2</label>
            <input type="file" class="form-control" id="image2" name="image2" accept="image/*">
        </div>
        <div class="mb-3">
            <label for="image3" class="form-label">Image 3</label>
            <input type="file" class="form-control" id="image3" name="image3" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary" id="submitBtn">Add Product</button>
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
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <?php if ($row['image1']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image1']); ?>" width="50" alt="Image 1">
                        <?php endif; ?>
                        <?php if ($row['image2']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image2']); ?>" width="50" alt="Image 2">
                        <?php endif; ?>
                        <?php if ($row['image3']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image3']); ?>" width="50" alt="Image 3">
                        <?php endif; ?>
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
                            <form action="admin_add_product.php" method="POST" enctype="multipart/form-data" class="edit-form">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="current_image1" value="<?php echo htmlspecialchars($row['image1']); ?>">
                                    <input type="hidden" name="current_image2" value="<?php echo htmlspecialchars($row['image2']); ?>">
                                    <input type="hidden" name="current_image3" value="<?php echo htmlspecialchars($row['image3']); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="edit_title_<?php echo $row['id']; ?>" class="form-label">Product Title</label>
                                        <input type="text" class="form-control" id="edit_title_<?php echo $row['id']; ?>" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_description_<?php echo $row['id']; ?>" class="form-label">Product Description</label>
                                        <textarea class="form-control" id="edit_description_<?php echo $row['id']; ?>" name="description" rows="4" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_price_<?php echo $row['id']; ?>" class="form-label">Product Price ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="edit_price_<?php echo $row['id']; ?>" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Current Image 1</label>
                                        <?php if ($row['image1']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($row['image1']); ?>" width="50" alt="Current Image 1">
                                        <?php endif; ?>
                                        <input type="file" class="form-control mt-2" name="image1" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Current Image 2</label>
                                        <?php if ($row['image2']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($row['image2']); ?>" width="50" alt="Current Image 2">
                                        <?php endif; ?>
                                        <input type="file" class="form-control mt-2" name="image2" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Current Image 3</label>
                                        <?php if ($row['image3']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($row['image3']); ?>" width="50" alt="Current Image 3">
                                        <?php endif; ?>
                                        <input type="file" class="form-control mt-2" name="image3" accept="image/*">
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
            <tr><td colspan="6">No products found.</td></tr>
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

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5
    }
}
</script>

</body>