<?php
session_start();
include 'config.php';

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php?success=removed");
    exit();
}

// Handle Update Cart Quantities
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header("Location: cart.php?success=updated");
    exit();
}

// Fetch products in the cart
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($ids) AND active = 1";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - E-commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .cart-container {
            margin-top: 30px;
        }
        .cart-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .cart-card img {
            object-fit: cover;
            width: 100%;
            height: 150px;
        }
        .cart-card-body {
            padding: 15px;
        }
        .cart-summary {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            border-radius: 10px;
            background-color: #fff;
        }
        .cart-summary h4 {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-commerce Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">Cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container cart-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                if ($_GET['success'] == 'removed') {
                    echo "Item removed from cart.";
                } elseif ($_GET['success'] == 'updated') {
                    echo "Cart updated successfully.";
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h1 class="mb-4">Your Cart</h1>

        <?php if (!empty($cart_items)): ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <form action="cart.php" method="POST">
                        <div class="row g-4">
                            <?php 
                            $total = 0;
                            foreach ($cart_items as $item): 
                                $subtotal = $item['price'] * $_SESSION['cart'][$item['id']];
                                $total += $subtotal;
                            ?>
                                <div class="col-md-6">
                                    <div class="card cart-card">
                                        <img src="uploads/<?php echo htmlspecialchars($item['image1']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                        <div class="card-body cart-card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                            <p class="card-text text-muted">Price: $<?php echo number_format($item['price'], 2); ?></p>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $_SESSION['cart'][$item['id']]; ?>" min="1" class="form-control form-control-sm w-25">
                                                <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                                            </div>
                                            <p class="mt-2"><strong>Subtotal:</strong> $<?php echo number_format($subtotal, 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                            <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                        </div>
                    </form>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4>Cart Summary</h4>
                        <p class="mt-3"><strong>Total:</strong> $<?php echo number_format($total, 2); ?></p>
                        <a href="checkout.php" class="btn btn-success w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Your cart is empty. <a href="products.php">Shop now</a></p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
