<?php
session_start();
include 'config.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Handle Checkout Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $total = 0;

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Calculate total price
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $query = "SELECT price FROM products WHERE id = ? AND active = 1";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if ($product) {
                $total += $product['price'] * $quantity;
            }
        }

        // Insert order into the database
        $order_query = "INSERT INTO orders (name, email, phone, address, total, payment_method, order_date, status) 
                        VALUES (?, ?, ?, ?, ?, 'COD', NOW(), 'Pending')";
        $stmt = mysqli_prepare($conn, $order_query);
        mysqli_stmt_bind_param($stmt, "ssssd", $name, $email, $phone, $address, $total);
        $success = mysqli_stmt_execute($stmt);

        if (!$success) {
            throw new Exception("Order insertion failed: " . mysqli_error($conn));
        }

        $order_id = mysqli_insert_id($conn);

        // Insert order details
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $order_details_query = "INSERT INTO order_details (order_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $order_details_query);
            mysqli_stmt_bind_param($stmt, "iii", $order_id, $product_id, $quantity);
            $success = mysqli_stmt_execute($stmt);

            if (!$success) {
                throw new Exception("Order details insertion failed: " . mysqli_error($conn));
            }
        }

        // Commit transaction
        mysqli_commit($conn);

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to confirmation page
        header("Location: confirmation.php?order_id=$order_id");
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo "Error processing order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .checkout-container {
            display: flex;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: 50px auto;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            overflow: hidden;
        }
        .checkout-image {
            flex: 1;
            background-image: url('assets/checkout-banner.jpg');
            background-size: cover;
            background-position: center;
            min-height: 400px;
        }
        .checkout-content {
            flex: 1;
            padding: 40px;
        }
        .checkout-header {
            margin-bottom: 30px;
        }
        .checkout-header h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .checkout-header p {
            color: #6c757d;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 8px;
            display: block;
            text-align: center;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-image">
            <img src="images/delivery1.png" alt="delivery boy " width="500" height="600">
        </div>

        <div class="checkout-content">
            <div class="checkout-header">
                <h2>Checkout</h2>
                <p>Complete your order with Cash on Delivery</p>
            </div>

            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label>Total Amount</label>
                    <p class="form-control-static">$<?php echo number_format(array_sum(array_map(function($product_id, $quantity) use ($conn) {
                        $query = "SELECT price FROM products WHERE id = ? AND active = 1";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "i", $product_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $product = mysqli_fetch_assoc($result);
                        return $product ? $product['price'] * $quantity : 0;
                    }, array_keys($_SESSION['cart']), $_SESSION['cart'])), 2); ?></p>
                </div>

                <button type="submit" class="btn btn-custom">Place Order</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
