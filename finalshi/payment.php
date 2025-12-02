<?php
session_start();
include 'connect.php';

// Security check
if (!isset($_SESSION['checkout_data']) || !isset($_SESSION['user'])) {
    header("Location: checkout.php");
    exit();
}

// Fix username warning
if (empty($_SESSION['user']['username'])) {
    $_SESSION['user']['username'] = $_SESSION['user']['email'] ?? 'User';
}

$data    = $_SESSION['checkout_data'];
$user_id = (int)$_SESSION['user']['user_id'];
$isLoggedIn = true; // we already checked above

// Logout handler (same as index.php)
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    echo "<script>alert('You have logged out successfully.'); window.location='guestindex.php';</script>";
    exit();
}

// Process payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare("INSERT INTO sales 
        (user_id, total_amount, payment_method, status, delivery_fee, city_id, municipality, province, phone, address) 
        VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idsiissss", $user_id, $data['grandTotal'], $payment_method, $data['delivery'],
        $data['city_id'], $data['city_name'], $data['province'], $data['phone'], $data['address']);

    if ($stmt->execute()) {
        $sale_id = $conn->insert_id;
        $stmt->close();

        foreach ($data['items'] as $item) {
            $conn->query("INSERT INTO sales_products (sale_id, product_id, quantity, price_at_purchase) 
                          VALUES ($sale_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})");
            $conn->query("UPDATE products SET quantity = quantity - {$item['quantity']} WHERE id = {$item['product_id']}");
        }
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");
        unset($_SESSION['checkout_data']);
        header("Location: purchaseSuccess.php?sale_id=$sale_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment - Thriftoes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="CSS/index.css" />
  <style>
    body { background:#0f0f0f; margin:0; }
    .payment-wrapper { max-width:700px; margin:100px auto 60px; background:#1a1a1a; padding:40px; border-radius:20px; box-shadow:0 20px 60px rgba(201,162,39,0.3); text-align:center; }
    .header h1 { color:#C9A227; font-size:2.5rem; }
    .total-box { background:linear-gradient(135deg,#C9A227,#e6c040); color:#000; padding:25px; border-radius:15px; font-size:2rem; font-weight:bold; margin:25px 0; }
    .info { margin:30px 0; color:#ddd; line-height:1.8; }
    .qr-box { background:#111; padding:30px; border-radius:15px; margin:30px 0; }
    .qr-box img { max-width:260px; border-radius:12px; }
    .btn { width:100%; padding:18px; font-size:1.3rem; border:none; border-radius:12px; margin:10px 0; cursor:pointer; font-weight:bold; }
    .gcash { background:linear-gradient(135deg,#C9A227,#e6c040); color:#000; }
    .bank  { background:#333; color:#fff; }
  </style>
</head>
<body>

<!-- EXACT SAME NAVBAR AS index.php -->
<header>
  <a href="index.php">
    <img src="Logo.png" class="logo" alt="ThrifToes Logo" />
  </a>

  <nav>
    <ul id="navbar">
      <li><a href="index.php">Home</a></li>
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
          <li><a href="toreceive.php">To Receive</a></li>
          <li><a href="purchaseHistory.php">Purchase History</a></li>
          <li><a href="?logout=true">Log Out</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</header>

<div class="payment-wrapper">
  <div class="header">
    <h1>Payment</h1>
    <p>Complete your order</p>
  </div>

  <div class="total-box">
    Total Amount: ₱<?= number_format($data['grandTotal'], 2) ?>
  </div>

  <div class="info">
    <strong>Deliver to:</strong><br>
    <?= nl2br(htmlspecialchars($data['address'])) ?><br>
    <?= htmlspecialchars($data['city_name']) ?>, <?= htmlspecialchars($data['province']) ?><br>
    <?= htmlspecialchars($data['phone']) ?>
  </div>

  <form method="POST">
    <div class="qr-box">
      <p><strong>Pay with GCash</strong></p>
      <img src="img/gcash-qr.jpg" alt="GCash QR">
      <p style="margin-top:15px;">Scan & Pay exact amount above</p>
    </div>

    <button type="submit" name="payment_method" value="gcash" class="btn gcash">
      I Paid with GCash
    </button>

    <button type="submit" name="payment_method" value="bank" class="btn bank">
      Pay via Bank Transfer
    </button>
  </form>

  <p style="margin-top:30px; color:#888;">
    Your order will be confirmed instantly after payment!
  </p>
</div>

<!-- SAME CART SCRIPT AS HOMEPAGE -->
<script>
document.getElementById("cart").addEventListener("click", function(e) {
  e.preventDefault();
  window.location.href = "cart.php";
});
</script>

</body>
</html>