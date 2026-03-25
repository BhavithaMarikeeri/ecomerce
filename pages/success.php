<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 1;

// Fetch cart items
$stmt = $conn->prepare("
    SELECT c.product_id, c.quantity, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Save order
$stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
$stmt->execute([$user_id, $total]);

$order_id = $conn->lastInsertId();

// Save order items
foreach ($cart_items as $item) {
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['price']
    ]);
}

// Clear cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
</head>
<body style="text-align:center; margin-top:100px;">

<h2 style="color:green;">✅ Payment Successful!</h2>
<p>Order ID: #<?= $order_id ?></p>

<a href="../index.php">
    <button>Continue Shopping</button>
</a>

</body>
</html>