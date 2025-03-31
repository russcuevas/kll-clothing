<?php
include 'database/connection.php';
session_start();  // Start the session

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Initialize cart items array
$cart_items = [];

// Check if the user is logged in and has cart items
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query to get cart items for the logged-in user
    $count_cart_items = $conn->prepare("SELECT * FROM `tbl_carts` WHERE user_id = ?");
    $count_cart_items->execute([$user_id]);
    $total_cart_items = $count_cart_items->rowCount();

    // Get cart items
    if ($total_cart_items > 0) {
        $query = "SELECT * FROM tbl_carts WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $cart_items = [];
    }
} else {
    $total_cart_items = 0;
}

// Fetch product details only if there are cart items
$products = [];
if (count($cart_items) > 0) {
    $product_query = "SELECT * FROM tbl_products WHERE id IN (" . implode(',', array_map('intval', array_column($cart_items, 'product_id'))) . ")";
    $product_stmt = $conn->query($product_query);
    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Process the cart items and products together
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="shortcut icon" href="assets/image/kll-favicon.jpg" type="image/x-icon">
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.all.min.js"></script>

</head>

<body>
    <nav id="navbar">
        <div class="logo" style="cursor: pointer;"><img onclick="window.location.href = 'index.php'"
                src="assets/image/kll-logo.png" alt="">
        </div>
        <div class="menu-toggle">
            <div class="hamburger" onclick="toggleMenu()">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <ul>
            <li><button class="close-menu" onclick="toggleMenu()">X</button></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="order.php">How to Order</a></li>
        </ul>
        <div class="icons">
            <?php if ($is_logged_in): ?>
                <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>
            <?php else: ?>
                <a href="#" onclick="toggleAuth(event)"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="auth-form" id="authForm">
        <button class="close-auth-btn" onclick="closeAuthForm()">
            <i class="fas fa-times"></i>
        </button>
        <div id="signInForm">
            <h2>Sign In</h2>
            <form method="POST" action="auth/login.php">
                <div class="form-group">
                    <label for="emailSignIn">Email address</label>
                    <input type="email" class="form-control" id="emailSignIn" name="email" aria-describedby="emailHelp" placeholder="Enter email" required>
                </div>
                <div class="form-group mb-2">
                    <label for="passwordSignIn">Password</label>
                    <input type="password" class="form-control" id="passwordSignIn" name="password" placeholder="Password" required>
                </div>
                <button id="btn-auth" type="submit" class="btn btn-primary">Login</button><br>
                <a id="createAccountBtn" href="javascript:void(0)" onclick="toggleCreateAccountForm()">Create a new one?</a><br>
            </form>
        </div>
        <div id="createAccountForm" style="display: none;">
            <?php if (isset($_SESSION['errorMessage'])): ?>
                <script>
                    // SweetAlert for error message
                    Swal.fire({
                        title: 'Error!',
                        text: '<?php echo $_SESSION['errorMessage']; ?>',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>
                <?php unset($_SESSION['errorMessage']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['successMessage'])): ?>
                <script>
                    // SweetAlert for success message
                    Swal.fire({
                        title: 'Success!',
                        html: '<?php echo $_SESSION['successMessage']; ?>',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                </script>
                <?php unset($_SESSION['successMessage']); ?>
            <?php endif; ?>

            <h2>Create Account</h2>
            <form method="POST" action="auth/register.php">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="first_name" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <label for="addressCreate">Address</label>
                    <input type="text" class="form-control" id="addressCreate" name="address" aria-describedby="addressHelp" placeholder="Enter address" required>
                </div>
                <div class="form-group">
                    <label for="emailCreate">Email address</label>
                    <input type="email" class="form-control" id="emailCreate" name="email" aria-describedby="emailHelp" placeholder="Enter email" required>
                </div>
                <div class="form-group mb-2">
                    <label for="passwordCreate">Password</label>
                    <input type="password" class="form-control" id="passwordCreate" name="password" placeholder="Password" required>
                </div>
                <button id="btn-auth" type="submit" class="btn btn-primary">Register</button><br>
                <a id="signInBtn">Already have an account?</a>
            </form>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Shopping Cart</h2>
        <div class="row">
            <div class="col-lg-8">
                <?php
                $total_price = 0;  // Initialize total price variable

                if (count($cart_items) > 0):
                    foreach ($cart_items as $cart_item):
                        // Find product details based on product_id
                        $product = null;
                        foreach ($products as $p) {
                            if ($p['id'] == $cart_item['product_id']) {
                                $product = $p;
                                break;
                            }
                        }
                        $quantity = 1;
                        $total_price += $product['product_price'] * $quantity;
                ?>
                        <div class="card p-3 mb-3" style="border: 2px solid black;">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="assets/image/<?php echo $product['product_image']; ?>" class="img-fluid" alt="Product Image">
                                </div>
                                <div class="col-md-6">
                                    <h5><?php echo $product['product_name']; ?></h5>
                                    <p>₱<?php echo number_format($product['product_price'], 2); ?></p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <a href="remove_from_cart.php?cart_item_id=<?php echo $cart_item['id']; ?>" class="btn btn-danger">Remove X</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h1 class="mt-5">Your cart is empty.</h1>
                <?php endif; ?>
            </div>
            <?php if (!empty($cart_items)): ?>
                <div class="col-lg-4">
                    <div class="card p-3" style="border: 2px solid black;">
                        <h4>Order Summary</h4><br>
                        <h5>Total: <span class="float-end" style="color: red;">₱<?php echo number_format($total_price, 2); ?></span></h5>
                        <a href="checkout.php" class="btn btn-primary w-100 mt-3" style="background-color: black; border: 2px solid black">
                            PROCEED TO CHECKOUT
                        </a>
                    </div>
                </div>
            <?php else: ?>

            <?php endif; ?>
        </div>
    </div>



    <div class="footer-section" style="margin-top: 100vh;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 order-md-3">
                    <div class="footer-right">
                        <h3>Order online / Face to face</h3>
                        <p>Check the details</p>
                        <a href="https://www.facebook.com/share/15p94e7FyW/" target="_blank" class="text-light"><i class="fa-brands fa-square-facebook"></i></a>
                        <a href="https://www.instagram.com/nimu.moves?igsh=ZWQ1cG51MTJiMml2" target="_blank" class="text-light"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://x.com/AbadCasinillo?s=09" target="_blank" class="text-light"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="footer-left">
                        <img src="assets/image/kll-logo.png" alt="Logo" class="img-fluid">
                        <h5 class="mt-3">© 2025 Made by Jonel, Justine, and Jamela</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>