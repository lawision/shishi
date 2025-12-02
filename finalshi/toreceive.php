<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user_id = (int)$_SESSION['user']['user_id'];

// Fetch orders that are pending, processing, or shipped (waiting to be received)
$stmt = $conn->prepare("
    SELECT * FROM sales 
    WHERE user_id = ? AND status IN ('pending','processing','shipped') 
    ORDER BY order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Receive - Thriftoes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/index.css">
    <style>
        .toreceive-wrapper {
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
            padding: 120px 20px;
            color: #888;
            font-size: 1.5rem;
        }
        .empty-state i {
            font-size: 6rem;
            color: #444;
            margin-bottom: 25px;
        }
        .empty-state a {
            display: inline-block;
            margin-top: 30px;
            padding: 16px 45px;
            background: linear-gradient(90deg, #C9A227, #f1d04b);
            color: #000;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 12px 35px rgba(201,162,39,0.5);
            transition: 0.4s;
        }
        .empty-state a:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(201,162,39,0.7);
        }

        .orders-grid {
            display: grid;
            gap: 28px;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        }

        .receive-card {
            background: #1a1a1a;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0,0,0,0.7);
            border: 2px solid #222;
            transition: all 0.5s ease;
            position: relative;
        }
        .receive-card:hover {
            transform: translateY(-15px);
            border-color: #C9A227;
            box-shadow: 0 35px 80px rgba(201,162,39,0.4);
        }

        .card-header {
            background: linear-gradient(135deg, #C9A227, #f1d04b);
            color: #000;
            padding: 22px 28px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .order-id {
            font-size: 1.6rem;
        }
        .status-badge {
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: bold;
        }
        .status-pending    { background: #333; color: #fff; }
        .status-processing { background: #ff9800; color: #000; }
        .status-shipped    { background: #2196F3; color: #fff; }

        .card-body {
            padding: 28px;
            color: #F8F8F4;
        }
        .card-body p {
            margin: 12px 0;
            font-size: 1.1rem;
        }
        .card-body strong { color: #C9A227; }

        .card-footer {
            padding: 0 28px 28px;
            text-align: center;
        }
        .btn-view {
            display: inline-block;
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, #C9A227, #f1d04b);
            color: #000;
            font-weight: bold;
            font-size: 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(201,162,39,0.5);
            transition: all 0.4s;
        }
        .btn-view:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px rgba(201,162,39,0.7);
        }

        @media (max-width: 768px) {
            .page-title { font-size: 2.4rem; }
            .card-header { flex-direction: column; text-align: center; }
            .orders-grid { grid-template-columns: 1fr; }
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
                        <li><a href="toreceive.php" class="active">To Receive</a></li>
                        <li><a href="purchaseHistory.php">Purchase History</a></li>
                        <li><a href="index.php?logout=true">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <div class="toreceive-wrapper">
        <h1 class="page-title">To Receive</h1>

        <?php if (empty($sales)): ?>
            <div class="empty-state">
                <i class="fas fa-truck"></i>
                <h2>No orders on the way yet!</h2>
                <p>All your paid/processing orders will appear here.</p>
                <a href="shop.php">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-grid">
                <?php foreach ($sales as $sale): ?>
                    <article class="receive-card">
                        <div class="card-header">
                            <div class="order-id">Order #<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></div>
                            <div class="status-badge status-<?= htmlspecialchars($sale['status']) ?>">
                                <?= ucfirst($sale['status']) ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <p><strong>Placed On:</strong><br>
                               <?= date('M d, Y - h:i A', strtotime($sale['order_date'])) ?></p>
                            <p><strong>Total Amount:</strong><br>
                               <span style="color:#C9A227;font-size:1.6rem;font-weight:bold;">
                                   ₱<?= number_format($sale['total_amount'], 2) ?>
                               </span>
                            </p>
                        </div>

                        <div class="card-footer">
                            <a href="purchaseSuccess.php?sale_id=<?= $sale['id'] ?>" class="btn-view">
                                View Order Details
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
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