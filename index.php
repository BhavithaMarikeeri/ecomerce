<?php
session_start();
include 'includes/db.php';

// Search functionality
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $conn->query("SELECT * FROM products");
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f5f5f5;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }

        .cart-icon {
            width: 20px;
            vertical-align: middle;
        }

        .search-box {
            margin: 20px;
            text-align: center;
        }

        .search-box input {
            padding: 8px;
            width: 250px;
        }

        .main-container {
            padding: 20px;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .product {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .add-to-cart-button {
            background: green;
            color: white;
            border: none;
            padding: 8px;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

<header>
    <div class="header-container">
        <h1>🛒 Online Store</h1>
        <nav>
            <a href="pages/login.php">Login</a>
            <a href="pages/register.php">Register</a>
            <a href="pages/cart.php">
                <img src="images/cart-icon.png" class="cart-icon"> Cart
            </a>
            <!-- ✅ ADDED THIS -->
            <a href="pages/orders.php">My Orders</a>
        </nav>
    </div>
</header>

<!-- 🔍 SEARCH -->
<div class="search-box">
    <form method="GET">
        <input type="text" name="search" placeholder="Search products...">
        <button type="submit">Search</button>
    </form>
</div>

<div class="main-container">
    <h2>Products</h2>

    <div class="product-list">
        <?php if (empty($products)) : ?>
            <p>No products available.</p>
        <?php else : ?>
            <?php foreach ($products as $product) : ?>
                <div class="product">

                    <?php if (!empty($product['image'])) : ?>
                        <img src="images/<?= htmlspecialchars($product['image']); ?>">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($product['name']); ?></h3>

                    <p><strong>₹<?= number_format($product['price'], 2); ?></strong></p>

                    <p><?= htmlspecialchars($product['description']); ?></p>

                    <form method="POST" action="pages/cart.php">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart-button">
                            Add to Cart
                        </button>
                    </form>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y'); ?> Online Store</p>
</footer>

</body>
</html>