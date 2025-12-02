<?php
session_start();
include 'connect.php'; // Make sure this exists

// Check login session
$isLoggedIn = isset($_SESSION['user']);

// Logout handler
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    echo "<script>alert('You have logged out successfully.'); window.location='guestindex.php';</script>";
    exit();
}

// Load Sale Promo From DB
$promoQuery = $conn->query("SELECT * FROM sale_promo WHERE id = 1");
$promo = $promoQuery->fetch_assoc();

// Dynamic Best Sellers
$bestSellers = [];
$bestSellerQuery = $conn->query("SELECT * FROM products WHERE quantity > 0 ORDER BY sold_count DESC LIMIT 4");

if ($bestSellerQuery) {
    while ($row = $bestSellerQuery->fetch_assoc()) {
        $bestSellers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HHH</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="CSS/index.css" />
</head>

<body>
  <header>
    <a href="#" id="logo-link">
      <img src="Logo.png" class="logo" alt="HHH Logo" />
    </a>

    <nav>
      <ul id="navbar">
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="shop.php">Shop</a></li>
        <li>
          <a href="#" aria-label="Shopping Cart" id="cart">
            <i class="fa-solid fa-cart-shopping"></i>
          </a>
        </li>

        <li class="submenu">
          <a href="#" aria-label="More Options">
            <i class="fa fa-bars" aria-hidden="true"></i>
          </a>
          <ul class="submenu-options">
            <?php if (!$isLoggedIn): ?>
              <li><a href="signup.php">Sign Up</a></li>
              <li><a href="login.php" id="login">Log In</a></li>
            <?php else: ?>
              <li><a href="toreceive.php">To Receive</a></li>
              <li><a href="purchaseHistory.php">Purchase History</a></li>
              <li><a href="index.php?logout=true">Log Out</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <!-- HERO SECTION -->
  <section class="hero">
    <video autoplay muted loop playsinline class="hero-video" poster="img/shoes-bg.jpg">
      <source src="bg.mp4" type="video/mp4" />
    </video>

    <div class="hero-content">
      <h1>Life is Short, Be Wise, Be Safe, Be Hard Headed</h1>
      <p>High-quality Premium Helmets at unbeatable prices find your perfect gear today!</p>
      <a href="shop.php" class="btn">Shop Now</a>
    </div>
  </section>

  <!-- BEST SELLERS DYNAMIC -->
  <section class="featured">
    <h2>Best Sellers</h2>
    <div class="helmet-grid">
      <?php if (!empty($bestSellers)): ?>
        <?php foreach ($bestSellers as $product): 
            // Determine correct image path
            $imagePath = !empty($product['image']) && file_exists($product['image']) ? $product['image'] : 'img/default-helmet.jpg';
        ?>
          <div class="helmet-item">
            <a href="viewproduct.php?id=<?= $product['id']; ?>">
              <img src="<?= htmlspecialchars($imagePath); ?>" alt="<?= htmlspecialchars($product['name']); ?>" />
            </a>
            <h3><?= htmlspecialchars($product['name']); ?></h3>
            <p><b>₱<?= number_format($product['price'], 2); ?></b></p>
            <p>Sold: <?= $product['sold_count']; ?></p>
            <a href="viewproduct.php?id=<?= $product['id']; ?>" class="btn">View Details</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products yet.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- SALE PROMO -->
  <section class="sale-promo">
    <div class="promo-content">
      <h2><?= htmlspecialchars($promo['title']); ?></h2>
      <p><?= htmlspecialchars($promo['description']); ?></p>
      <a href="shop.php" class="btn">View Sale Helmets</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-container">
      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="guestindex.php">Home</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="#" id="cart-footer">Cart</a></li>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Return Policy</a></li>
        </ul>
      </div>

      <div class="footer-section contact">
        <h3>Contact Us</h3>
        <p>Email: support@hhh.com</p>
        <p>Phone: 09227777777</p>
        <p>Address: 123 Safety St, Mandaue City</p>
      </div>

      <div class="footer-section social">
        <h3>Follow Us</h3>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; 2025 HHH. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Logo redirect based on login status
    document.getElementById("logo-link").addEventListener("click", function(e) {
      e.preventDefault();
      <?php if (!$isLoggedIn): ?>
        window.location.href = "guestindex.php";
      <?php else: ?>
        window.location.href = "index.php";
      <?php endif; ?>
    });

    // Cart links
    document.getElementById("cart").addEventListener("click", function(e) {
      <?php if (!$isLoggedIn): ?>
        e.preventDefault();
        window.location.href = "login.php";
      <?php else: ?>
        window.location.href = "cart.php";
      <?php endif; ?>
    });

    document.getElementById("cart-footer").addEventListener("click", function(e) {
      <?php if (!$isLoggedIn): ?>
        e.preventDefault();
        window.location.href = "login.php";
      <?php else: ?>
        window.location.href = "cart.php";
      <?php endif; ?>
    });
  </script>

</body>
</html>
