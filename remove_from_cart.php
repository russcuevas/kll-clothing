<?php
include 'database/connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errorMessage = '';
$successMessage = '';

if (isset($_GET['cart_item_id'])) {
    $cart_item_id = $_GET['cart_item_id'];

    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM tbl_carts WHERE id = :cart_item_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cart_item_id', $cart_item_id);
    $stmt->bindParam(':user_id', $user_id);

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['successMessage'] = 'Product remove to cart successfully';
        header("Location: cart.php");
        exit();
    } else {
        $_SESSION['errorMessage'] = 'Failed to remove item from cart.';
        header("Location: cart.php");
        exit();
    }
} else {
    header("Location: cart.php");
    exit();
}
