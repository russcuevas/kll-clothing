<?php
include 'database/connection.php';
session_start();  // Start the session

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

//check cart
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $count_cart_items = $conn->prepare("SELECT * FROM `tbl_carts` WHERE user_id = ?");
    $count_cart_items->execute([$user_id]);
    $total_cart_items = $count_cart_items->rowCount();
} else {
    $total_cart_items = 0;
}

// Fetch product data
$products_query = "SELECT * FROM tbl_products";
$products_stmt = $conn->query($products_query);
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLL Clothing</title>
    <link rel="shortcut icon" href="assets/image/kll-favicon.jpg" type="image/x-icon">
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
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
            <!-- SWEETALERT -->
            <?php if (isset($_SESSION['errorMessage'])): ?>
                <script>
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



    <div class="main-section">
        <div class="container">
        </div>
    </div>

    <div class="products-section">
        <div class="container">
            <div class="container-title">
                <h3 class="mr-auto">KLL P.E UNIFORM</h3>
            </div>
            <div class="row text-center">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="products">
                            <a href="#">
                                <img id="product-image-<?php echo $product['id']; ?>" src="assets/image/<?php echo $product['product_image']; ?>" alt="">
                            </a>
                            <div class="products-details">
                                <p id="product-name-<?php echo $product['id']; ?>"><?php echo $product['product_name']; ?></p>
                                <p><span class="price" id="product-price-<?php echo $product['id']; ?>">₱<?php echo number_format($product['product_price'], 2); ?></span></p>
                                <button class="btn btn-success add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>


                <!-- Modal for Size Selection -->
                <!-- Modal for Size Selection -->
                <div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sizeModalLabel">Select Size</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="add_to_cart.php">
                                <div class="modal-body">
                                    <!-- Hidden Product ID Field -->
                                    <input type="hidden" name="product_id" id="product_id" value="">

                                    <!-- Product Image -->
                                    <div class="text-center">
                                        <img id="modalProductImage" src="" alt="Product Image" class="img-fluid" style="max-height: 200px;">
                                    </div>

                                    <!-- Product Name -->
                                    <div class="text-center mt-3">
                                        <h5 id="modalProductName"></h5>
                                    </div>

                                    <!-- Product Price -->
                                    <div class="text-center mt-2">
                                        <p id="modalProductPrice" class="text-success"></p>
                                    </div>

                                    <!-- Size Selection -->
                                    <div class="form-group">
                                        <label for="sizeSelect">Select Size</label>
                                        <select class="form-control" id="sizeSelect" name="size" required>
                                            <option value="Extra Small">Extra Small (XS)</option>
                                            <option value="Small">Small (S)</option>
                                            <option value="Medium">Medium (M)</option>
                                            <option value="Large">Large (L)</option>
                                            <option value="Extra Large">Extra Large (XL)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Proceed</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>




                <div class="reviews-section">
                    <div class="container mt-5">
                        <div class="reviews-title">
                            <h2>KLL Student Reviews</h2>
                            <p>from 3 reviews</p>
                        </div>
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="review-card">
                                        <div class="review-header">
                                            <h3>Justine *</h3>
                                        </div>
                                        <div class="review-body">
                                            <p>"The material is breathable, preventing excessive sweating and discomfort
                                                during
                                                exercise."</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="review-card">
                                        <div class="review-header">
                                            <h3>Jamela *</h3>
                                        </div>
                                        <div class="review-body">
                                            <p>"The uniform fits well and allows for ease of movement, making it
                                                comfortable for
                                                physical activities."</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="review-card">
                                        <div class="review-header">
                                            <h3>Jonel *</h3>
                                        </div>
                                        <div class="review-body">
                                            <p>"The design is simple yet stylish, making students look neat and
                                                presentable."</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>

                <div class="features-section">
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="top-box mb-3">
                                    <img src="assets/image/pe-uniform.jpg" alt="Rectangle Box" class="img-fluid">
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <div class="square-box">
                                            <img src="assets/image/t-shirt.jpg" alt="Square Box" class="img-fluid">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="square-box">
                                            <img src="assets/image/pants.jpeg" alt="Square Box" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="big-box">
                                    <img src="assets/image/pe-model.jpg" alt="Big Box" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="footer-section">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 order-md-3">
                                <div class="footer-right">
                                    <h3>Order online / Face to face</h3>
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
                <script>
                    function addToCart(productId) {
                        var isLoggedIn = <?php echo json_encode($is_logged_in); ?>;

                        if (isLoggedIn) {
                            var productName = document.getElementById('product-name-' + productId).innerText;
                            var productPrice = document.getElementById('product-price-' + productId).innerText;
                            var productImage = document.getElementById('product-image-' + productId).src;

                            document.getElementById('modalProductImage').src = productImage;
                            document.getElementById('modalProductName').innerText = productName;
                            document.getElementById('modalProductPrice').innerText = productPrice;
                            document.getElementById('product_id').value = productId;

                            $('#sizeModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Login Required',
                                text: 'Please log in first to add items to the cart.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                </script>
</body>

</html>