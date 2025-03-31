<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLL Clothing</title>
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
                src="assets/image/kll-logo.png" alt=""></div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="order.php">How to Order</a></li>
        </ul>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Contact Us</h2>
        <p class="text-center">Feel free to reach out to us for any inquiries.</p>
        <div class="row">
            <div class="col-md-5 mt-4">
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Your Name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Your Email">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="4" placeholder="Your Message"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <div class="col-md-6 offset-md-1 mt-4">
    <h4>Our Contact Details</h4>

    <p>
        <i class="fas fa-map-marker-alt"></i>
        <a href="https://maps.app.goo.gl/fWuHgc74tv579PpWA" target="_blank" class="text-dark">
            Kolehiyo ng Lungsod ng Lipa
        </a>
    </p>

    <p>
        <i class="fas fa-envelope"></i>
        <a href="https://www.facebook.com/KLLOfficial" target="_blank" class="text-dark">
            support@kllclothing.com
        </a>
    </p>

    <p class="mt-2">
        <i class="fas fa-phone me-2"></i>
        <a href="tel:+639093511090" class="text-dark fw-bold">
            +63909-351-1090
        </a>
    </p>

    <!-- Social Media Icons Moved Here -->
    <div class="mt-3">
        <a href="https://www.facebook.com/share/15p94e7FyW/" target="_blank" class="text-dark me-2">
            <i class="fab fa-facebook fa-2x"></i>
        </a>
        <a href="https://www.instagram.com/nimu.moves?igsh=ZWQ1cG51MTJiMml2" target="_blank" class="text-dark me-2">
            <i class="fab fa-instagram fa-2x"></i>
        </a>
        <a href="https://x.com/AbadCasinillo?s=09" target="_blank" class="text-dark">
            <i class="fab fa-twitter fa-2x"></i>
        </a>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>