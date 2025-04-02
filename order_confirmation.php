<?php
include 'database/connection.php';
session_start();

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$is_logged_in) {
    // If not logged in, redirect to login page
    header("Location: auth/login.php");
    exit();
}

// Check if order ID is provided in the URL
$order_id = $_GET['order_id'] ?? null;
if ($order_id) {
    // Fetch order details based on the reference number
    $stmt = $conn->prepare("SELECT * FROM tbl_placed_order WHERE reference_number = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Fetch product details for the ordered products
        $products_ordered = explode(", ", $order['products_ordered']);
    } else {
        $error_message = "Order not found.";
    }
} else {
    $error_message = "Invalid order reference.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="shortcut icon" href="assets/image/kll-favicon.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container my-5">
        <div class="text-center">
            <img src="assets/image/kll-logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
            <h1 class="text-danger">Thank You for Your Order! 🎉</h1>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error_message; ?>
                </div>
            <?php else: ?>
                <div class="card p-4 shadow-sm">
                    <h4>Your Order Details</h4>
                    <p><strong>Reference Number:</strong> <?= $order['reference_number']; ?></p>
                    <p><strong>Full Name:</strong> <?= $order['fullname']; ?></p>
                    <p><strong>Email:</strong> <?= $order['email']; ?></p>
                    <p><strong>Contact Number:</strong> <?= $order['contact']; ?></p>
                    <p><strong>Address:</strong> <?= $order['address']; ?></p>
                    <p><strong>Payment Method:</strong> <?= $order['payment_method']; ?></p>
                    <p><strong>Total Price:</strong> ₱<?= number_format($order['total_price'], 2); ?></p>

                    <h5>Products Ordered:</h5>
                    <ul class="list-unstyled">
                        <?php foreach ($products_ordered as $product): ?>
                            <li><?= $product; ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Display images based on products ordered -->
                    <div class="mt-4">
                        <?php
                        // Check if "KLL PE-PANTS" or "KLL PE-SHIRT" is in any of the ordered products
                        foreach ($products_ordered as $product) {
                            if (strpos($product, "KLL PE-PANTS") !== false) {
                                echo '<img src="assets/image/pe-pants.jpg" alt="KLL PE Pants" class="img-fluid mb-3" style="max-width: 200px; margin-right: 20px;">';
                            }
                            if (strpos($product, "KLL PE-SHIRT") !== false) {
                                echo '<img src="assets/image/pe-shirts.jpg" alt="KLL PE Shirt" class="img-fluid mb-3" style="max-width: 200px;">';
                            }
                        }
                        ?>
                    </div>

                    <div class="text-success mt-3">
                        <h5>Your order will be processed shortly. 😊</h5>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-danger btn-sm">Go to Home</a>
                    <a href="cart.php" class="btn btn-danger btn-sm">View Cart</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>