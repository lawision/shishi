<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user']['user_id'];

// Messages (from actions)
$message = $_SESSION['cart_message'] ?? null;
unset($_SESSION['cart_message']);

// Fetch cart items
$sql = "
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.brand, p.price, p.image, p.status
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate subtotal
$subtotal = 0;
foreach ($cartItems as $it) {
    $subtotal += ($it['price'] * $it['quantity']);
}

// Get price per km from settings
$price_per_km_result = $conn->query("SELECT value FROM settings WHERE key_name='price_per_km'");
$price_per_km = $price_per_km_result ? ($price_per_km_result->fetch_assoc()['value'] ?? 2.0) : 2.0;

// Default delivery (will be replaced once address is selected in checkout)
$deliveryFee = empty($cartItems) ? 0 : 50; // Base fee only — real fee calculated in checkout
$total = $subtotal + $deliveryFee;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Your Cart - ThrifToes</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="CSS/cart.css">
<style>
    .delivery-notice {
        background: rgba(201,162,39,0.15);
        border: 1px solid #C9A227;
        color: #C9A227;
        padding: 12px 15px;
        border-radius: 10px;
        font-size: 0.95rem;
        margin: 15px 0;
        text-align: center;
    }
    .delivery-notice strong { color: #fff; }
</style>
</head>
<body>

<!-- =========================== NAVIGATION =========================== -->
<header class="navbar-container">
  <div class="nav-left">
    <a href="index.php" class="logo-link">
      <img src="Logo.png" class="logo-img" alt="ThrifToes Logo">
    </a>
  </div>

  <nav class="nav-center">
    <ul class="nav-links">
      <li><a href="index.php" <?= basename($_SERVER['PHP_SELF'])=='index.php' ? 'class="active"' : '' ?>>Home</a></li>
      <li><a href="shop.php" <?= basename($_SERVER['PHP_SELF'])=='shop.php' ? 'class="active"' : '' ?>>Shop</a></li>
    </ul>
  </nav>

  <nav class="nav-right">
    <ul class="nav-links">
      <li>
        <a href="#" id="cart" class="active">
          <i class="fa-solid fa-cart-shopping"></i>
        </a>
      </li>
      <li class="submenu">
        <a class="menu-icon"><i class="fa fa-bars"></i></a>
        <ul class="submenu-options">
          <li><a href="toreceive.php">To Receive</a></li>
          <li><a href="purchaseHistory.php">Purchase History</a></li>
          <li><a href="index.php?logout=true">Log Out</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</header>

<!-- ================================================================= -->

<main class="container">
  <h1 class="page-title">Your Shopping Cart</h1>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <div class="cart-grid">
    <div class="cart-list">
      <form method="post" action="clear_cart.php" class="clear-form" onsubmit="return confirm('Clear all items from your cart?');">
        <button type="submit" class="clear-btn">Clear Cart</button>
        <a href="shop.php" class="continue-btn">Continue Shopping</a>
      </form>

      <div class="table-wrap">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Brand</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Total</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($cartItems)): ?>
              <tr><td colspan="7" class="empty">Your cart is empty.</td></tr>
            <?php else: foreach ($cartItems as $item):
              $lineTotal = $item['price'] * $item['quantity']; ?>
              <tr>
                <td><img src="<?= htmlspecialchars($item['image']) ?>" class="cart-img" alt=""></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['brand']) ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td>
                  <div class="qty-controls">
                    <form method="post" action="update_quantity.php" class="qty-form inline">
                      <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                      <input type="hidden" name="action" value="dec">
                      <button class="qty-btn">-</button>
                    </form>
                    <span class="qty-value"><?= $item['quantity'] ?></span>
                    <form method="post" action="update_quantity.php" class="qty-form inline">
                      <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                      <input type="hidden" name="action" value="inc">
                      <button class="qty-btn">+</button>
                    </form>
                  </div>
                </td>
                <td>₱<?= number_format($lineTotal, 2) ?></td>
                <td><a href="remove_from_cart.php?id=<?= $item['cart_id'] ?>" class="remove-link"><i class="fa fa-trash"></i></a></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <aside class="cart-summary">
      <h2>Order Summary</h2>
      <div class="summary-row"><span>Subtotal</span><span>₱<?= number_format($subtotal, 2) ?></span></div>
      
      <!-- Dynamic Delivery Notice -->
      <div class="delivery-notice">
        <strong>Delivery Fee:</strong> Starting from ₱50<br>
        <small>Exact amount will be calculated in checkout based on your city</small>
      </div>

      <hr>
      <div class="summary-row total"><span>Total (approx.)</span><span>₱<?= number_format($subtotal + 50, 2) ?>+</span></div>

      <form method="post" action="checkout.php">
        <button type="submit" class="checkout-btn" <?= empty($cartItems) ? 'disabled' : '' ?>>
          Proceed to Checkout
        </button>
      </form>
    </aside>
  </div>
</main>

<script>
document.getElementById("cart").addEventListener("click", function(e) {
  e.preventDefault();
  window.location.href = "cart.php";
});
</script>

</body>
</html>