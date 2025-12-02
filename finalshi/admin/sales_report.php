<?php
include 'admin_protect.php';
include '../connect.php';

// Date filter
$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end']   ?? date('Y-m-d');

$start = date('Y-m-d', strtotime($start));
$end   = date('Y-m-d', strtotime($end));

// MAIN QUERY — using table "user"
$sql = "
    SELECT 
        s.id AS sale_id,
        s.order_date,
        s.total_amount,
        s.payment_method,
        s.status,
        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
        u.contact_number,
        COUNT(sp.product_id) AS items_count,
        SUM(sp.quantity) AS total_qty
    FROM sales s
    LEFT JOIN user u ON s.user_id = u.user_id
    LEFT JOIN sales_products sp ON s.id = sp.sale_id
    WHERE DATE(s.order_date) BETWEEN ? AND ?
    GROUP BY s.id
    ORDER BY s.order_date DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Database Error: " . $conn->error);
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$result = $stmt->get_result();
$sales = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Summary Stats
$stats = $conn->query("
    SELECT 
        COUNT(*) AS total_orders,
        COALESCE(SUM(total_amount), 0) AS total_revenue,
        COALESCE(AVG(total_amount), 0) AS avg_order_value,
        SUM(CASE WHEN payment_method = 'gcash' THEN 1 ELSE 0 END) AS gcash_count,
        SUM(CASE WHEN payment_method = 'bank' THEN 1 ELSE 0 END) AS bank_count
    FROM sales 
    WHERE DATE(order_date) BETWEEN '$start' AND '$end'
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Admin</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root{
            --black:#000;
            --yellow:#FFC107;
            --dark:#222;
        }
        body{background:#000;color:#fff;font-family:'Segoe UI',sans-serif;margin:0;}
        
        /* SAME FIXED NAVBAR AS DASHBOARD */
        .admin-header{
            position:fixed;top:0;left:0;right:0;
            background:var(--black);
            padding:15px 0;
            z-index:1000;
            box-shadow:0 4px 20px rgba(0,0,0,.9);
        }
        .admin-logo{color:var(--yellow);font-size:1.9em;font-weight:900;text-decoration:none;}
        .nav-links a{color:#ccc;transition:.3s;}
        .nav-links a:hover,.nav-links a.active{color:var(--yellow);}
        .btn-logout{
            background:var(--yellow);color:#000;
            padding:10px 25px;border-radius:50px;font-weight:bold;
        }

        /* PREVENT CONTENT OVERLAP */
        .admin-container{
            padding:120px 30px 60px;
            min-height:100vh;
        }

        .report-wrapper{
            max-width:1300px;
            margin:0 auto;
        }
        .page-title{
            text-align:center;
            font-size:2.8em;
            color:var(--yellow);
            margin:0 0 30px;
            font-weight:900;
            text-shadow:0 3px 10px rgba(255,193,7,.5);
        }
        .date-range{
            text-align:center;
            color:#aaa;
            font-size:1.2em;
            margin-bottom:30px;
        }

        /* FILTERS */
        .filters{
            background:var(--dark);
            padding:20px;
            border-radius:12px;
            display:flex;
            justify-content:center;
            gap:20px;
            flex-wrap:wrap;
            margin-bottom:40px;
            box-shadow:0 5px 20px rgba(0,0,0,.5);
        }
        .filters input{
            padding:12px;
            border:1px solid #444;
            border-radius:8px;
            background:#333;
            color:#fff;
            font-size:1em;
        }
        .filters button{
            background:var(--yellow);
            color:#000;
            border:none;
            padding:12px 30px;
            border-radius:50px;
            font-weight:bold;
            cursor:pointer;
            transition:.3s;
        }
        .filters button:hover{background:#e6ac00;}

        /* STATS CARDS */
        .stats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:25px;
            margin-bottom:50px;
        }
        .stat-card{
            background:var(--dark);
            padding:30px;
            border-radius:12px;
            text-align:center;
            box-shadow:0 10px 30px rgba(0,0,0,.6);
            border:2px solid transparent;
            transition:.4s;
        }
        .stat-card:hover{
            border-color:var(--yellow);
            transform:translateY(-10px);
        }
        .stat-card h3{
            color:var(--yellow);
            font-size:1.5em;
            margin:0;
            font-weight:900;
        }
        .stat-card p{
            color:#aaa;
            margin:10px 0 0;
            font-size:1.1em;
        }

        /* TABLE */
        table{
            width:100%;
            border-collapse:collapse;
            background:var(--dark);
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 10px 40px rgba(0,0,0,.7);
        }
        th{
            background:var(--black);
            color:var(--yellow);
            padding:18px;
            text-align:left;
            font-weight:700;
            font-size:1.05em;
        }
        td{
            padding:16px 18px;
            color:#ddd;
            border-bottom:1px solid #333;
        }
        tr:hover{
            background:#1a1a1a;
        }
        .badge{
            padding:6px 16px;
            border-radius:30px;
            font-weight:bold;
            font-size:0.9em;
        }
        .badge.gcash{background:#333;color:var(--yellow);}
        .badge.bank{background:#333;color:var(--yellow);}
        .badge.pending{background:#444;color:#ff9800;}
        .badge.completed{background:#004d00;color:#00ff00;}

        .no-data{
            text-align:center;
            padding:80px 20px;
            color:#666;
            font-size:1.4em;
        }
        strong{color:var(--yellow);}
    </style>
</head>
<body class="admin-page">

<!-- SAME NAVBAR AS DASHBOARD -->
<header class="admin-header">
    <nav class="admin-nav">
        <div class="nav-left">
            <a href="dashboard.php" class="admin-logo">Admin Panel</a>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="sales_report.php" class="active">Sales</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
</header>

<main class="admin-container">
    <div class="report-wrapper">
        <h1 class="page-title">SALES REPORT</h1>
        <div class="date-range">
            <?= date('F d, Y', strtotime($start)) ?> to <?= date('F d, Y', strtotime($end)) ?>
        </div>

        <div class="filters">
            <form method="GET">
                <input type="date" name="start" value="<?= $start ?>" required>
                <input type="date" name="end" value="<?= $end ?>" required>
                <button type="submit">Apply Filter</button>
                <a href="sales_report.php" style="color:var(--yellow);text-decoration:underline;margin-left:20px;">Reset</a>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>₱<?= number_format($stats['total_revenue'], 2) ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['total_orders'] ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3>₱<?= number_format($stats['avg_order_value'], 2) ?></h3>
                <p>Average Order</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['gcash_count'] ?></h3>
                <p>GCash Payments</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['bank_count'] ?></h3>
                <p>Bank Transfers</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date & Time</th>
                    <th>Customer Name</th>
                    <th>Contact No.</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sales)): ?>
                    <tr><td colspan="8" class="no-data">No sales found in this period.</td></tr>
                <?php else: foreach ($sales as $sale): ?>
                    <tr>
                        <td><strong>#<?= str_pad($sale['sale_id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= date('M d, Y • h:i A', strtotime($sale['order_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['customer_name'] ?? 'Guest User') ?></td>
                        <td><?= htmlspecialchars($sale['contact_number'] ?? '—') ?></td>
                        <td><?= $sale['total_qty'] ?> item<?= $sale['total_qty']>1?'s':'' ?></td>
                        <td><strong>₱<?= number_format($sale['total_amount'], 2) ?></strong></td>
                        <td><span class="badge <?= $sale['payment_method'] ?>">
                            <?= $sale['payment_method']=='gcash'?'GCash':'Bank Transfer' ?>
                        </span></td>
                        <td><span class="badge <?= $sale['status']=='completed'?'completed':'pending' ?>">
                            <?= ucfirst($sale['status'] ?? 'pending') ?>
                        </span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>