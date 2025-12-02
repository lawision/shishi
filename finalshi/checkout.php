<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit(); 
}

// FIX: Prevent "undefined username" warning
if (isset($_SESSION['user']) && empty($_SESSION['user']['username'])) {
    $_SESSION['user']['username'] = $_SESSION['user']['email'] ?? 'User';
}

$user_id = (int)$_SESSION['user']['user_id'];

// Fetch cart
$stmt = $conn->prepare("SELECT c.quantity, p.id AS product_id, p.price, p.name, p.brand, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$items = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($items)) {
    $_SESSION['cart_message'] = "Your cart is empty.";
    header("Location: cart.php");
    exit();
}

$subtotal = 0;
foreach ($items as $it) $subtotal += $it['price'] * $it['quantity'];

$price_per_km = $conn->query("SELECT value FROM settings WHERE key_name='price_per_km'")->fetch_assoc()['value'] ?? 2.0;
$all_cities = $conn->query("SELECT id, name, province, distance_from_cebu_km FROM cities ORDER BY province, name")->fetch_all(MYSQLI_ASSOC);

$selected_province = $_POST['province'] ?? '';
$selected_city_id = $_POST['city_id'] ?? 0;
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

// Calculate delivery
$delivery = 0;
$city_name = $province_name = $distance_km = '';

if ($selected_city_id && $selected_province) {
    foreach ($all_cities as $c) {
        if ($c['id'] == $selected_city_id && $c['province'] === $selected_province) {
            $distance_km = (float)$c['distance_from_cebu_km'];
            $delivery = $distance_km <= 10 ? 50 : 50 + ($distance_km - 10) * $price_per_km;
            $city_name = $c['name'];
            $province_name = $c['province'];
            break;
        }
    }
}

$grandTotal = $subtotal + $delivery;

// Save data and go to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $delivery > 0 && $phone && $address && $selected_city_id) {
    $_SESSION['checkout_data'] = [
        'items' => $items,
        'subtotal' => $subtotal,
        'delivery' => $delivery,
        'grandTotal' => $grandTotal,
        'city_id' => $selected_city_id,
        'city_name' => $city_name,
        'province' => $province_name,
        'phone' => $phone,
        'address' => $address
    ];
    header("Location: payment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Thriftoes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="CSS/cart.css">
    <style>
        .checkout-wrapper{max-width:1200px;margin:80px auto 40px;background:#1a1a1a;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(201,162,39,0.2);}
        .checkout-header{background:linear-gradient(135deg,#C9A227,#e6c040);color:#000;padding:30px;text-align:center;font-size:2.2rem;font-weight:700;}
        .checkout-body{display:grid;grid-template-columns:1fr 1fr;}
        .order-summary,.payment-area{padding:40px;}
        .order-summary{background:#111;border-right:2px solid #222;}
        .item{display:flex;gap:18px;padding:18px 0;border-bottom:1px solid #333;}
        .item img{width:90px;height:90px;object-fit:cover;border-radius:12px;border:2px solid #333;}
        .summary-total p{display:flex;justify-content:space-between;margin:15px 0;color:#fff;font-weight:bold;}
        .grand-total{font-size:1.5rem!important;color:#C9A227!important;border-top:2px solid #333;padding-top:15px;}
        .address-section{background:#1c1c1c;padding:25px;border-radius:12px;margin-bottom:25px;border:2px solid #333;}
        .address-section select,.address-section input,.address-section textarea{width:100%;padding:12px;margin:10px 0;background:#111;border:1px solid #444;color:#fff;border-radius:8px;font-size:1rem;}
        .confirm-btn{width:100%;padding:15px;background:linear-gradient(135deg,#C9A227,#e6c040);color:#000;border:none;border-radius:12px;font-size:1.2rem;font-weight:bold;cursor:pointer;margin-top:20px;}
        .shipping-display{background:rgba(201,162,39,0.15);padding:15px;border-radius:12px;text-align:center;margin:15px 0;color:#C9A227;font-weight:bold;}
    </style>
</head>
<body>

<!-- SAME HEADER AS CART.PHP -->
<header class="navbar-container">
    <div class="nav-left">
        <a href="index.php" class="logo-link">
            <img src="Logo.png" class="logo-img" alt="ThrifToes Logo">
        </a>
    </div>
    <nav class="nav-center">
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php">Products</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
    <div class="nav-right">
        <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a>
        <div class="user-menu">
            <span>Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</header>

<div class="checkout-wrapper">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p>Fill up your delivery details</p>
    </div>

    <div class="checkout-body">
        <div class="order-summary">
            <h2 style="color:#C9A227;">Order Summary</h2>
            <?php foreach ($items as $it): ?>
            <div class="item">
                <img src="<?=htmlspecialchars($it['image'])?>" alt="">
                <div class="item-info">
                    <h4><?=htmlspecialchars($it['name'])?></h4>
                    <p><?=htmlspecialchars($it['brand'])?> × <?=$it['quantity']?></p>
                    <strong style="color:#C9A227;">₱<?=number_format($it['price']*$it['quantity'],2)?></strong>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="summary-total">
                <p><span>Subtotal:</span> <span>₱<?=number_format($subtotal,2)?></span></p>
                <p><span>Delivery Fee:</span> <span style="color:#C9A227;"><?=$delivery>0?'₱'.number_format($delivery,2):'Select city'?></span></p>
                <p class="grand-total"><span>Total:</span> <span>₱<?=number_format($grandTotal,2)?></span></p>
            </div>
        </div>

        <div class="payment-area">
            <form method="POST" class="address-section">
                <h3 style="color:#C9A227;">Delivery Information</h3>

                <label>Province</label>
                <select name="province" onchange="this.form.submit()" required>
                    <option value="">-- Select Province --</option>
                    <?php
                    $provinces = array_unique(array_column($all_cities, 'province'));
                    sort($provinces);
                    foreach ($provinces as $p) {
                        $sel = ($selected_province === $p) ? 'selected' : '';
                        echo "<option value=\"".htmlspecialchars($p)."\" $sel>".htmlspecialchars($p)."</option>";
                    }
                    ?>
                </select>

                <label>City / Municipality</label>
                <select name="city_id" required>
                    <option value="">-- Select City --</option>
                    <?php foreach ($all_cities as $c): 
                        if ($c['province'] === $selected_province): ?>
                            <option value="<?=$c['id']?>" <?=($selected_city_id == $c['id']?'selected':'')?>><?=$c['name']?></option>
                    <?php endif; endforeach; ?>
                </select>

                <label>Phone Number</label>
                <input type="text" name="phone" value="<?=htmlspecialchars($phone)?>" placeholder="09123456789" required maxlength="11">

                <label>Complete Address (House #, Street, Barangay, Landmark)</label>
                <textarea name="address" rows="4" placeholder="Ex: Block 5 Lot 12, Phase 2, Brgy. Guadalupe, near 7-Eleven" required><?=htmlspecialchars($address)?></textarea>

                <?php if($delivery > 0): ?>
                <div class="shipping-display">
                    Delivery: ₱<?=number_format($delivery,2)?><br>
                    <small><?=$city_name?>, <?=$province_name?></small>
                </div>
                <?php endif; ?>

                <button type="submit" class="confirm-btn">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>