<?php
session_start();
include '../includes/db.php';

// Protect admin panel
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch products
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f7fa;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 12px;
            background: green;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #28a745;
            color: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        img {
            width: 50px;
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .edit {
            background: orange;
            color: white;
        }

        .delete {
            background: red;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>📦 Manage Products</h2>

    <div class="top-bar">
        <a href="add_product.php" class="btn">➕ Add Product</a>
        <a href="dashboard.php" class="btn">🏠 Dashboard</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td>₹<?= $product['price'] ?></td>
            <td><?= htmlspecialchars($product['description']) ?></td>
            <td>
                <img src="../images/<?= htmlspecialchars($product['image']) ?>">
            </td>
            <td class="actions">
                <a href="edit_product.php?id=<?= $product['id'] ?>" class="edit">Edit</a>
                <a href="delete_product.php?id=<?= $product['id'] ?>" 
                   class="delete"
                   onclick="return confirm('Delete this product?')">
                   Delete
                </a>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>