<?php
session_start();
include '../includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['user_id'] ?? 1;

// Handle "Pay Now" click
if (isset($_POST['pay_now'])) {

    // Fetch cart items
    $stmt = $conn->prepare("
        SELECT c.product_id, c.quantity, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (!empty($cart_items)) {

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

        $order_success = true;
    }
}

// Fetch cart for display
$stmt = $conn->prepare("
    SELECT c.quantity, p.name, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            text-align: center;
        }
        .card {
            background: white;
            padding: 40px;
            margin: 100px auto;
            width: 400px;
            border-radius: 10px;
        }
        .success {
            color: green;
            font-size: 22px;
        }
        button {
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="card">

<?php if (isset($order_success)): ?>

    <h2 class="success">✅ Payment Successful!</h2>
    <p>Order ID: #<?= $order_id ?></p>

    <a href="../index.php">
        <button>Continue Shopping</button>
    </a>

<?php elseif ($items): ?>

    <h2>Checkout</h2>

    <?php foreach ($items as $item): ?>
        <p>
            <?= $item['name']; ?> -
            ₹<?= $item['price']; ?> × <?= $item['quantity']; ?>
        </p>
    <?php endforeach; ?>

    <h3>Total: ₹<?= $total ?></h3>

    <form method="POST">
        <button type="submit" name="pay_now">
            Pay Now
        </button>
    </form>

<?php else: ?>

    <h2>Your cart is empty</h2>

    <a href="../index.php">
        <button>Go Shopping</button>
    </a>

<?php endif; ?>

</div>

</body>
</html>