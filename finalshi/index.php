<?php
session_start();
include 'connect.php'; // <-- required

// Check login session
$isLoggedIn = isset($_SESSION['user']);

// Logout handler
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    echo "<script>alert('You have logged out successfully.'); window.location='guestindex.php';</script>";
    exit();
}

// GET SALE PROMO FROM DATABASE
$promoQuery = $conn->query("SELECT * FROM sale_promo WHERE id = 1");
$promo = $promoQuery->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thriftoes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="CSS/index.css" />
</head>

<body>
  <header>
    <a href="index.php">
      <img src="Logo.png" class="logo" alt="" />
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

        <!-- MENU OPTIONS CHANGE IF LOGGED IN -->
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

  <!-- ================= HERO SECTION ================= -->
  <section class="hero">
    <video autoplay muted loop playsinline class="hero-video" poster="img/shoes-bg.jpg">
      <source src="bg.mp4" type="video/mp4" />
    </video>

    <div class="hero-content">
      <h1>Life is Short, Be Wise, Be Safe, Be Hard Headed</h1>
      <p>Unique, high-quality thrifted shoes at unbeatable prices—find your perfect pair today!</p>
      <a href="shop.php" class="btn">Shop Now</a>
    </div>
  </section>

  <!-- ================= FEATURED ================= -->
  <section class="featured">
    <h2>Best Sellers</h2>
    <div class="shoe-grid">
      <div class="shoe-item">
        <img src="img/shoe1.jpg" alt="Sneakers" />
        <h3>Urban Classic</h3>
        <p>Stylish comfort for everyday adventures.</p>
        <a href="shop.php" class="btn">View Details</a>
      </div>

      <div class="shoe-item">
        <img src="img/shoe2.jpg" alt="Vintage Shoe" />
        <h3>Retro Street</h3>
        <p>Throwback vibes with premium durability.</p>
        <a href="shop.php" class="btn">View Details</a>
      </div>

      <div class="shoe-item">
        <img src="img/shoe3.jpg" alt="Running Shoe" />
        <h3>Runner Pro</h3>
        <p>Built for speed, designed for comfort.</p>
        <a href="shop.php" class="btn">View Details</a>
      </div>
    </div>
  </section>

  <!-- ================= SALE PROMO (DYNAMIC) ================= -->
  <section class="sale-promo">
    <div class="promo-content">
      <h2><?php echo $promo['title']; ?></h2>
      <p><?php echo $promo['description']; ?></p>
      <a href="shop.php" class="btn">Shop the Sale</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-container">

      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="#" id="cart-footer">Cart</a></li>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Return Policy</a></li>
        </ul>
      </div>

      <div class="footer-section contact">
        <h3>Contact Us</h3>
        <p>Email: support@thriftoes.com</p>
        <p>Phone: 09227777777</p>
        <p>Address: 123 Thrift St, Mandaue City, ST</p>
      </div>

      <div class="footer-section social">
        <h3>Follow Us</h3>
        <div class="social-links">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; 2025 Thriftoes. All rights reserved.</p>
    </div>
  </footer>

  <!-- CART CHECKER -->
  <script>
  document.getElementById("cart").addEventListener("click", function(e) {
    <?php if (!$isLoggedIn): ?>
      e.preventDefault();
      alert("Log in first");
      window.location.href = "login.php";
    <?php else: ?>
      window.location.href = "cart.php";
    <?php endif; ?>
  });

  document.getElementById("cart-footer").addEventListener("click", function(e) {
    <?php if (!$isLoggedIn): ?>
      e.preventDefault();
      alert("Log in first");
      window.location.href = "login.php";
    <?php else: ?>
      window.location.href = "cart.php";
    <?php endif; ?>
  });
  </script>

</body>
</html>
