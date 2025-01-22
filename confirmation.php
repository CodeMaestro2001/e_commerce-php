<?php
session_start();
include 'config.php';

if (!isset($_GET['order_id'])) {
    echo "Invalid order ID.";
    exit();
}

$order_id = intval($_GET['order_id']); // Sanitize input

// Fetch order details
$order_query = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch ordered items (use the correct column name)
$order_details_query = "SELECT od.product_id, od.quantity, p.title, p.price 
                        FROM order_details od 
                        JOIN products p ON od.product_id = p.id 
                        WHERE od.order_id = ?";
$stmt = mysqli_prepare($conn, $order_details_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .order-summary {
            margin-top: 20px;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h2>Order Confirmation</h2>
        <p>Thank you for your order, <strong><?php echo htmlspecialchars($order['name']); ?></strong>!</p>
        <p>Your order ID is <strong>#<?php echo $order['id']; ?></strong></p>
        <p>Total Amount: <strong>$<?php echo number_format($order['total'], 2); ?></strong></p>
        <p>Payment Method: <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong></p>
        <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>

        <h3 class="order-summary">Order Summary</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="index.php" class="btn-back">Return to Home</a>
    </div>
</body>
</html>
