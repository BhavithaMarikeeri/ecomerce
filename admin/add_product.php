<?php
include '../includes/db.php';

if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $image = $_FILES['image']['name'];
    $target = "../images/" . basename($image);

    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $description, $image]);

    echo "✅ Product Added!";
}
?>

<h2>Add Product</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required><br><br>
    <input type="number" name="price" placeholder="Price" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <input type="file" name="image" required><br><br>

    <button type="submit" name="add_product">Add Product</button>
</form>