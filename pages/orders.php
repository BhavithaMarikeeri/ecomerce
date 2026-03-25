<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 1;

// Fetch orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        .container {
            width: 80%;
            margin: 40px auto;
        }

        .order {
            background: white;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .order-header {
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .product {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .product img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }

        .product-name {
            font-weight: bold;
        }

        .total {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🧾 My Orders</h2>

    <?php if ($orders): ?>

        <?php foreach ($orders as $order): ?>

            <div class="order">

                <div class="order-header">
                    <p><strong>Order #<?= $order['id'] ?></strong></p>
                    <p>Date: <?= $order['created_at'] ?></p>
                </div>

                <?php
                // Fetch products inside this order
                $stmt = $conn->prepare("
                    SELECT oi.quantity, p.name, p.image, oi.price
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $items = $stmt->fetchAll();
                ?>

                <?php foreach ($items as $item): ?>
                    <div class="product">
                        <img src="../images/<?= $item['image'] ?>">
                        <div>
                            <div class="product-name"><?= $item['name'] ?></div>
                            <div>₹<?= $item['price'] ?> × <?= $item['quantity'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total">
                    Total: ₹<?= $order['total'] ?>
                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>
        <p>No orders found</p>
    <?php endif; ?>

</div>

</body>
</html>