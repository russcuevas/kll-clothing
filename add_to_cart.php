<?php
include 'database/connection.php';
session_start();

$errorMessage = '';
$successMessage = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$size = $_POST['size'];

$query = "INSERT INTO tbl_carts (user_id, product_id, size) VALUES (:user_id, :product_id, :size)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':product_id', $product_id);
$stmt->bindParam(':size', $size);

if ($stmt->execute()) {
    $_SESSION['successMessage'] = 'Product added to cart successfully';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    $_SESSION['errorMessage'] = 'Failed add to cart';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
