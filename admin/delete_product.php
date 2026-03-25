<?php
include '../includes/db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: manage_products.php");
?>