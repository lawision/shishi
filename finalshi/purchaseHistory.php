<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user_id = (int)$_SESSION['user']['user_id'];

// Fetch sales for this user
$stmt = $conn->prepare("SELECT * FROM sales WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$salesRes = $stmt->get_result();
$sales = $salesRes->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - Thriftoes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/index.css">
    <style>
        .history-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .page-title {
            text-align: center;
            font-size: 3rem;
            color: #C9A227;
            margin: 40px 0 60px;
            font-weight: 800;
            text-shadow: 0 5px 20px rgba(201,162,39,0.4);
        }

        .empty-state {
            text-align: center;
            padding: 100px 20px;
            color: #888;
            font-size: 1.4rem;
        }
        .empty-state i {
            font-size: 5rem;
            color: #444;
            margin-bottom: 20px;
        }
        .empty-state a {
            display: inline-block;
            margin-top: 30px;
            padding: 16px 40px;
            background: linear-gradient(90deg, #C9A227, #f1d04b);
            color: #000;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 10px 30px rgba(201,162,39,0.5);
            transition: 0.4s;
        }
        .empty-state a:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(201,162,39,0.7);
        }

        .order-card {
            background: #1a1a1a;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
            border: 2px solid #222;
            transition: all 0.4s ease;
        }
        .order-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 70px rgba(201,162,39,0.3);
            border-color: #C9A227;
        }

        .order-header {
            background: linear-gradient(135deg, #C9A227, #f1d04b);
            color: #000;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            font-weight: bold;
        }
        .order-id {
            font-size: 1.5rem;
        }
        .order-date {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .status-badge {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 0.95rem;
        }
        .status-pending { background: #333; color: #fff; }
        .status-processing { background: #ff9800; color: #000; }
        .status-shipped { background: #2196F3; color: #fff; }
        .status-delivered { background: #4CAF50; color: #fff; }
        .status-cancelled { background: #f44336; color: #fff; }

        .order-items {
            padding: 25px 30px;
            background: #111;
        }
        .item-row {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 18px 0;
            border-bottom: 1px solid #333;
        }
        .item-row:last-child { border-bottom: none; }
        .item-row img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #333;
        }
        .item-info h4 {
            margin: 0 0 8px;
            font-size: 1.2rem;
            color: #F8F8F4;
        }
        .item-info p {
            margin: 4px 0;
            color: #aaa;
            font-size: 0.95rem;
        }
        .item-price {
            margin-left: auto;
            font-size: 1.3rem;
            color: #C9A227;
            font-weight: bold;
        }

        .order-footer {
            padding: 25px 30px;
            background: #0D0D0D;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .total-amount {
            font-size: 1.8rem;
            color: #C9A227;
            font-weight: bold;
        }
        .order-actions {
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.4s;
        }
        .btn-view {
            background: linear-gradient(90deg, #C9A227, #f1d04b);
            color: #000;
            box-shadow: 0 8px 25px rgba(201,162,39,0.4);
        }
        .btn-view:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(201,162,39,0.6);
        }
        .btn-print {
            background: transparent;
            color: #C9A227;
            border: 2px solid #C9A227;
        }
        .btn-print:hover {
            background: #C9A227;
            color: #000;
            transform: translateY(-5px);
        }

        @media (max-width: 768px) {
            .order-header { flex-direction: column; text-align: center; }
            .item-row { flex-direction: column; text-align: center; }
            .item-price { margin: 10px 0 0; }
            .order-footer { flex-direction: column; text-align: center; }
            .order-actions { justify-content: center; width: 100%; }
            .btn { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- SAME HEADER AS ALL PAGES -->
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
                        <li><a href="purchaseHistory.php" class="active">Purchase History</a></li>
                        <li><a href="index.php?logout=true">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <div class="history-wrapper">
        <h1 class="page-title">Your Purchase History</h1>

        <?php if (empty($sales)): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h2>No orders yet!</h2>
                <p>Looks like you haven't made any purchases.</p>
                <a href="shop.php">Start Shopping Now</a>
            </div>
        <?php else: ?>
            <?php foreach ($sales as $sale): ?>
                <?php
                $stmt = $conn->prepare("
                    SELECT sp.quantity, sp.price_at_purchase, p.name, p.brand, p.image
                    FROM sales_products sp
                    JOIN products p ON sp.product_id = p.id
                    WHERE sp.sale_id = ?
                ");
                $stmt->bind_param("i", $sale['id']);
                $stmt->execute();
                $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                ?>

                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">Order #<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></div>
                        <div class="order-date"><?= date('M d, Y - h:i A', strtotime($sale['order_date'])) ?></div>
                        <div class="status-badge status-<?= htmlspecialchars($sale['status']) ?>">
                            <?= ucfirst($sale['status']) ?>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php foreach ($items as $it): ?>
                            <div class="item-row">
                                <img src="<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['name']) ?>">
                                <div class="item-info">
                                    <h4><?= htmlspecialchars($it['name']) ?></h4>
                                    <p>Brand: <?= htmlspecialchars($it['brand']) ?> • Qty: <?= $it['quantity'] ?></p>
                                </div>
                                <div class="item-price">
                                    ₱<?= number_format($it['price_at_purchase'] * $it['quantity'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-footer">
                        <div class="total-amount">
                            Total: ₱<?= number_format($sale['total_amount'], 2) ?>
                        </div>
                        <div class="order-actions">
                            <a href="purchaseSuccess.php?sale_id=<?= $sale['id'] ?>" class="btn btn-view">
                                View Details
                            </a>
                            <a href="print_receipt.php?sale_id=<?= $sale['id'] ?>" target="_blank" class="btn btn-print">
                                Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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