<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user']['user_id'];
$sale_id = isset($_GET['sale_id']) ? (int)$_GET['sale_id'] : 0;

if ($sale_id <= 0) {
    $error = "Invalid order reference.";
} else {
    $stmt = $conn->prepare("SELECT s.*, u.first_name, u.last_name FROM sales s JOIN user u ON s.user_id = u.user_id WHERE s.id = ? AND s.user_id = ?");
    $stmt->bind_param("ii", $sale_id, $user_id);
    $stmt->execute();
    $sale = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$sale) {
        $error = "Order not found.";
    } else {
        $stmt2 = $conn->prepare("
            SELECT sp.quantity, sp.price_at_purchase, p.name, p.brand, p.image
            FROM sales_products sp
            JOIN products p ON sp.product_id = p.id
            WHERE sp.sale_id = ?
        ");
        $stmt2->bind_param("i", $sale_id);
        $stmt2->execute();
        $items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt2->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed #<?= str_pad($sale_id, 6, '0', STR_PAD_LEFT) ?> - Thriftoes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/index.css">
    <style>
        .success-wrapper {
            max-width: 1100px;
            margin: 40px auto;
            background: #111;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(201,162,39,0.3);
            border: 2px solid #222;
        }
        .success-header {
            background: linear-gradient(135deg, #C9A227, #f1d04b);
            color: #000;
            text-align: center;
            padding: 50px 20px;
        }
        .check-icon {
            font-size: 90px;
            color: #000;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        .success-header h1 {
            font-size: 3rem;
            margin: 10px 0;
            font-weight: 800;
        }
        .success-header p {
            font-size: 1.4rem;
            opacity: 0.9;
        }

        .success-body {
            padding: 50px;
            color: #F8F8F4;
        }
        .order-info {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 18px;
            margin-bottom: 30px;
            border: 1px solid #333;
        }
        .order-info h2 {
            color: #C9A227;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            font-size: 1.1rem;
        }
        .info-grid strong { color: #C9A227; }

        .payment-instruction {
            background: rgba(201,162,39,0.15);
            border: 2px dashed #C9A227;
            border-radius: 18px;
            padding: 30px;
            margin: 35px 0;
            text-align: center;
        }
        .payment-instruction h3 {
            color: #C9A227;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        .qr-code img {
            width: 280px;
            height: 280px;
            border-radius: 20px;
            border: 8px solid #C9A227;
            box-shadow: 0 15px 40px rgba(0,0,0,0.8);
        }
        .instructions {
            margin-top: 25px;
            background: #1a1a1a;
            padding: 20px;
            border-radius: 14px;
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .bank-info {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 14px;
            line-height: 2;
            font-size: 1.15rem;
        }

        .items-grid {
            display: grid;
            gap: 20px;
            margin: 40px 0;
        }
        .item-card {
            display: flex;
            background: #1a1a1a;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #333;
            transition: 0.4s;
        }
        .item-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(201,162,39,0.3);
        }
        .item-card img {
            width: 140px;
            height: 140px;
            object-fit: cover;
        }
        .item-details {
            padding: 25px;
            flex: 1;
        }
        .item-details h3 {
            margin: 0 0 10px;
            font-size: 1.3rem;
            color: #F8F8F4;
        }
        .item-details p {
            margin: 8px 0;
            color: #aaa;
        }
        .item-details .price {
            font-size: 1.4rem;
            color: #C9A227;
            font-weight: bold;
        }

        .action-buttons {
            text-align: center;
            margin: 50px 0 20px;
        }
        .btn {
            display: inline-block;
            padding: 16px 40px;
            margin: 0 15px;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.4s;
            font-size: 1.1rem;
        }
        .btn-primary {
            background: linear-gradient(90deg, #C9A227, #f1d04b);
            color: #000;
            box-shadow: 0 10px 30px rgba(201,162,39,0.5);
        }
        .btn-primary:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(201,162,39,0.7);
        }
        .btn-outline {
            border: 2px solid #C9A227;
            color: #C9A227;
            background: transparent;
        }
        .btn-outline:hover {
            background: #C9A227;
            color: #000;
            transform: translateY(-6px);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @media (max-width: 768px) {
            .success-header h1 { font-size: 2.3rem; }
            .success-body { padding: 30px 20px; }
            .item-card { flex-direction: column; text-align: center; }
            .item-card img { width: 100%; height: 200px; }
            .btn { display: block; margin: 15px auto; width: 90%; }
        }
    </style>
</head>
<body>

    <!-- SAME HEADER AS HOMEPAGE -->
    <header>
        <a href="index.php"><img src="Logo.png" class="logo" alt="Thriftoes" /></a>
        <nav>
            <ul id="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="#" id="cart"><i class="fa-solid fa-cart-shopping"></i></a></li>
                <li class="submenu">
                    <a href="#"><i class="fa fa-bars"></i></a>
                    <ul class="submenu-options">
                        <li><a href="toreceive.php">To Receive</a></li>
                        <li><a href="purchaseHistory.php">Purchase History</a></li>
                        <li><a href="index.php?logout=true">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <div class="success-wrapper">
        <div class="success-header">
            <div class="check-icon">
                <i class="fas fa-circle-check"></i>
            </div>
            <h1>Thank You, <?= htmlspecialchars($sale['first_name'] ?? '') ?>!</h1>
            <p>Your order has been successfully placed</p>
        </div>

        <div class="success-body">
            <?php if (!empty($error)): ?>
                <div style="text-align:center; padding:80px; color:#ff6b6b;">
                    <h2><?= $error ?></h2>
                    <a href="shop.php" class="btn btn-primary">Back to Shop</a>
                </div>
            <?php else: ?>

                <div class="order-info">
                    <h2>Order Details</h2>
                    <div class="info-grid">
                        <p><strong>Order ID:</strong> #<?= str_pad($sale_id, 6, '0', STR_PAD_LEFT) ?></p>
                        <p><strong>Date:</strong> <?= date('M d, Y - h:i A', strtotime($sale['order_date'])) ?></p>
                        <p><strong>Payment:</strong> <?= $sale['payment_method'] == 'gcash' ? 'GCash' : 'Bank Transfer' ?></p>
                        <p><strong>Total Paid:</strong> <span style="color:#C9A227;font-size:1.5rem;font-weight:bold;">₱<?= number_format($sale['total_amount'], 2) ?></span></p>
                    </div>
                </div>

                <!-- PAYMENT INSTRUCTIONS -->
                <?php if ($sale['payment_method'] == 'gcash'): ?>
                    <div class="payment-instruction">
                        <h3>Complete Payment via GCash</h3>
                        <div class="qr-code">
                            <img src="img/gcash-qr.jpg" alt="GCash QR Code">
                        </div>
                        <div class="instructions">
                            <strong>Amount to Pay: ₱<?= number_format($sale['total_amount'], 2) ?></strong><br><br>
                            Steps:<br>
                            1. Open GCash → Tap "Scan QR"<br>
                            2. Scan the QR code above<br>
                            3. Enter exact amount<br>
                            4. Tap Pay → Send screenshot to our FB page<br><br>
                            <em style="color:#C9A227;">We'll ship your order within 24hrs after payment confirmation!</em>
                        </div>
                    </div>
                <?php elseif ($sale['payment_method'] == 'bank'): ?>
                    <div class="payment-instruction">
                        <h3>Complete Payment via Bank Transfer</h3>
                        <div class="bank-info">
                            <p><strong>BPI</strong><br>Account Name: ThrifToes Helmets<br>Account No: <strong>1234 5678 9012</strong></p>
                            <hr style="border-color:#444;margin:20px 0;">
                            <p><strong>BDO</strong><br>Account Name: ThrifToes Helmets<br>Account No: <strong>9876 5432 1098</strong></p>
                            <p style="margin-top:25px; color:#ff6b6b;">
                                Send proof of payment + Order #<?= str_pad($sale_id, 6, '0', STR_PAD_LEFT) ?> to our Facebook page.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <h2 style="color:#C9A227; margin:40px 0 25px;">Items Purchased</h2>
                <div class="items-grid">
                    <?php foreach ($items as $item): ?>
                        <div class="item-card">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <p>Brand: <?= htmlspecialchars($item['brand']) ?></p>
                                <p>Quantity: <?= $item['quantity'] ?></p>
                                <p class="price">₱<?= number_format($item['price_at_purchase'] * $item['quantity'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="action-buttons">
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="purchaseHistory.php" class="btn btn-outline">View All Orders</a>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <script>
        // Cart redirect
        document.getElementById("cart")?.addEventListener("click", function(e) {
            e.preventDefault();
            window.location.href = "cart.php";
        });
    </script>
</body>
</html>