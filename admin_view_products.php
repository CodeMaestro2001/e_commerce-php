<?php
include 'config.php'; // Database connection

// Handle Delete Request (Soft Delete)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "UPDATE products SET active = 0 WHERE id = $delete_id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Product deleted successfully!'); window.location='admin_view_products.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle Edit Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle updated images
    $image1 = $_FILES['image1']['name'] ?: $_POST['current_image1'];
    $image2 = $_FILES['image2']['name'] ?: $_POST['current_image2'];
    $image3 = $_FILES['image3']['name'] ?: $_POST['current_image3'];

    $target_dir = "uploads/";
    if ($_FILES['image1']['name']) {
        move_uploaded_file($_FILES['image1']['tmp_name'], $target_dir . $image1);
    }
    if ($_FILES['image2']['name']) {
        move_uploaded_file($_FILES['image2']['tmp_name'], $target_dir . $image2);
    }
    if ($_FILES['image3']['name']) {
        move_uploaded_file($_FILES['image3']['tmp_name'], $target_dir . $image3);
    }

    $sql = "UPDATE products 
            SET title = '$title', description = '$description', 
                image1 = '$image1', image2 = '$image2', image3 = '$image3' 
            WHERE id = $edit_id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Product updated successfully!'); window.location='admin_view_products.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch active products
$sql = "SELECT * FROM products WHERE active = 1";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Product List</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <?php if ($row['image1']) { ?>
                        <img src="uploads/<?php echo $row['image1']; ?>" width="50">
                    <?php } ?>
                    <?php if ($row['image2']) { ?>
                        <img src="uploads/<?php echo $row['image2']; ?>" width="50">
                    <?php } ?>
                    <?php if ($row['image3']) { ?>
                        <img src="uploads/<?php echo $row['image3']; ?>" width="50">
                    <?php } ?>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                    <a href="admin_view_products.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="admin_view_products.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="current_image1" value="<?php echo $row['image1']; ?>">
                                <input type="hidden" name="current_image2" value="<?php echo $row['image2']; ?>">
                                <input type="hidden" name="current_image3" value="<?php echo $row['image3']; ?>">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Product Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $row['title']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Product Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $row['description']; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image1" class="form-label">Image 1</label>
                                    <input type="file" class="form-control" id="image1" name="image1">
                                    <img src="uploads/<?php echo $row['image1']; ?>" width="50">
                                </div>
                                <div class="mb-3">
                                    <label for="image2" class="form-label">Image 2</label>
                                    <input type="file" class="form-control" id="image2" name="image2">
                                    <img src="uploads/<?php echo $row['image2']; ?>" width="50">
                                </div>
                                <div class="mb-3">
                                    <label for="image3" class="form-label">Image 3</label>
                                    <input type="file" class="form-control" id="image3" name="image3">
                                    <img src="uploads/<?php echo $row['image3']; ?>" width="50">
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
            <!-- End Edit Modal -->
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
