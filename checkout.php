<?php
include 'database/connection.php';
session_start();  // Start the session

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$is_logged_in) {
    // If not logged in, redirect to login page
    header("Location: auth/login.php");
    exit();
}

// Fetch the cart items for the logged-in user
$cart_items = [];
if ($is_logged_in) {
    $count_cart_items = $conn->prepare("SELECT * FROM `tbl_carts` WHERE user_id = ?");
    $count_cart_items->execute([$user_id]);
    $total_cart_items = $count_cart_items->rowCount();

    if ($total_cart_items > 0) {
        $query = "SELECT * FROM tbl_carts WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $cart_items = [];
    }
}

// Fetch product details only if there are cart items
$products = [];
if (count($cart_items) > 0) {
    $product_query = "SELECT * FROM tbl_products WHERE id IN (" . implode(',', array_map('intval', array_column($cart_items, 'product_id'))) . ")";
    $product_stmt = $conn->query($product_query);
    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Process the cart items and products together
$total_price = 0;  // Initialize total price variable
foreach ($cart_items as $cart_item) {
    foreach ($products as $product) {
        if ($product['id'] == $cart_item['product_id']) {
            $total_price += $product['product_price'];
        }
    }
}

// Insert the order into the `tbl_placed_order` table after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user input from the form
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $reference_number = uniqid('ORD-'); // Generate a unique reference number (you can adjust this)

    // Initialize an array for the ordered products
    $products_ordered = [];

    // Populate the products_ordered array with product details from the cart
    if (count($cart_items) > 0) {
        foreach ($cart_items as $cart_item) {
            $product = null;
            foreach ($products as $p) {
                if ($p['id'] == $cart_item['product_id']) {
                    $product = $p;
                    break;
                }
            }
            // Add the product name and size to the products_ordered array
            if ($product) {
                $products_ordered[] = $product['product_name'] . " (Size: " . $cart_item['size'] . ")";
            }
        }
    }

    // Insert into tbl_placed_order
    $stmt = $conn->prepare("INSERT INTO `tbl_placed_order` (user_id, products_ordered, reference_number, payment_method, total_price, fullname, email, address, contact) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $user_id,
        implode(", ", $products_ordered), // Combine the ordered products into a single string
        $reference_number,
        $payment_method,
        $total_price,
        $fullname,
        $email,
        $address,
        $contact
    ]);

    // Optionally, you can clear the cart after placing the order
    $clear_cart = $conn->prepare("DELETE FROM tbl_carts WHERE user_id = ?");
    $clear_cart->execute([$user_id]);

    // Redirect to a confirmation page or order summary page
    header("Location: order_confirmation.php?order_id=" . $reference_number);
    exit();
}

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
            <form>
                <div class="form-group">
                    <label for="emailSignIn">Email address</label>
                    <input type="email" class="form-control" id="emailSignIn" aria-describedby="emailHelp" placeholder="Enter email">
                </div>
                <div class="form-group mb-2">
                    <label for="passwordSignIn">Password</label>
                    <input type="password" class="form-control" id="passwordSignIn" placeholder="Password">
                </div>
                <button id="btn-auth" type="submit" class="btn btn-primary">Login</button><br>
                <a id="createAccountBtn">Create a new one?</a><br>
                <a href="#">Forgot Password?</a>
            </form>
        </div>
        <div id="createAccountForm" style="display: none;">
            <h2>Create Account</h2>
            <form>
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" placeholder="First Name">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" placeholder="Last Name">
                </div>
                <div class="form-group">
                    <label for="emailCreate">Email address</label>
                    <input type="email" class="form-control" id="emailCreate" aria-describedby="emailHelp" placeholder="Enter email">
                </div>
                <div class="form-group mb-2">
                    <label for=" passwordCreate">Password</label>
                    <input type="password" class="form-control" id="passwordCreate" placeholder="Password">
                </div>
                <button id="btn-auth" type="submit" class="btn btn-primary">Register</button><br>
                <a id="signInBtn">Already have an account?</a>
            </form>
        </div>
    </div>

    <!-- Checkout Start -->
    <form action="" method="POST">
        <input type="hidden" name="total_price" value="<?php echo number_format($total_price, 2); ?>">
        <div class="container-fluid pt-5">
            <div class="row px-xl-5">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <h4 class="font-weight-semi-bold mb-4">Your information</h4>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Fullname</label>
                                <input class="form-control" name="fullname" type="text" style="border: 2px solid #541111 !important;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>E-mail</label>
                                <input class="form-control" name="email" type="text" style="border: 2px solid #541111 !important;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Mobile No</label>
                                <input class="form-control" name="contact" type="text" style="border: 2px solid #541111 !important;">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Address</label>
                                <input class="form-control" name="address" type="text" style="border: 2px solid #541111 !important;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-primary mb-5" style="border: 2px solid #541111 !important;">
                        <div class="card-header border-0" style="background-color: #541111 !important; color: white !important">
                            <h4 class="font-weight-semi-bold m-0">Order Total</h4>
                        </div>
                        <?php
                        // Displaying all cart items and their associated products with sizes
                        if (count($cart_items) > 0):
                            foreach ($cart_items as $cart_item):
                                $product = null;
                                foreach ($products as $p) {
                                    if ($p['id'] == $cart_item['product_id']) {
                                        $product = $p;
                                        break;
                                    }
                                }
                        ?>
                                <input type="hidden" name="products_ordered" value="<?php echo $product['product_name'] ?> - <?php echo $cart_item['size'] ?>">

                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex">
                                            <img src="assets/image/<?php echo $product['product_image']; ?>" alt="<?php echo $product['product_name']; ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                            <p><?php echo $product['product_name']; ?> (Size: <?php echo $cart_item['size']; ?>)</p> <!-- Displaying the product name and size -->
                                        </div>
                                        <p>₱<?php echo number_format($product['product_price'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Your cart is empty.</p>
                        <?php endif; ?>

                        <div class="card-footer bg-transparent" style="border-top: 2px solid #541111 !important">
                            <div class="d-flex justify-content-between mt-2">
                                <h5 class="font-weight-bold">Total</h5>
                                <h5 class="font-weight-bold">₱<?php echo number_format($total_price, 2); ?></h5>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="card border-primary mb-5" style="border: 2px solid #541111 !important;">
                        <div class="card-header border-0" style="background-color: #541111 !important; color: white !important">
                            <h4 class="font-weight-semi-bold m-0">Payment</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <label class="custom-control-label" for="cash_on_delivery">Select Payment: </label>
                                    <select name="payment_method" class="custom-control-input" id="">
                                        <option value="Cash on pickup">Cash on pickup</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent" style="border-top: 2px solid #541111 !important;">
                            <button type="submit" class="btn btn-sm btn-block font-weight-bold my-3 py-3" style="float: right !important; background-color: #541111; color: white !important">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Checkout End -->

    <div class="footer-section">
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>