<?php
include 'admin_protect.php';
include '../connect.php';

// Totals
$t_users    = $conn->query("SELECT COUNT(*) AS c FROM `user`")->fetch_assoc()['c'];
$t_products = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$t_sales    = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS s FROM sales")->fetch_assoc()['s'];

// Weekly / Monthly / Yearly data (same as before)
$weeks = $week_revenues = [];
for ($i = 7; $i >= 0; $i--) {
    $start = date('Y-m-d', strtotime("-$i weeks", strtotime('monday this week')));
    $end   = date('Y-m-d', strtotime($start . ' +6 days'));
    $label = date('M d', strtotime($start)) . '–' . date('d', strtotime($end));
    $res = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS rev FROM sales WHERE order_date BETWEEN '$start' AND '$end 23:59:59'")->fetch_assoc();
    $weeks[] = $label;
    $week_revenues[] = (float)$res['rev'];
}

$months = $month_revenues = [];
for ($i = 11; $i >= 0; $i--) {
    $date = date('Y-m-01', strtotime("-$i months"));
    $label = date('M Y', strtotime($date));
    $res = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS rev FROM sales WHERE DATE_FORMAT(order_date, '%Y-%m') = DATE_FORMAT('$date', '%Y-%m')")->fetch_assoc();
    $months[] = $label;
    $month_revenues[] = (float)$res['rev'];
}

$years = $year_revenues = [];
for ($i = 5; $i >= 0; $i--) {
    $year = date('Y') - $i;
    $res = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS rev FROM sales WHERE YEAR(order_date) = $year")->fetch_assoc();
    $years[] = $year;
    $year_revenues[] = (float)$res['rev'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root{--black:#000;--yellow:#FFC107;--dark:#222;}
        body{background:#000;color:#fff;font-family:'Segoe UI',sans-serif;margin:0;}
        .admin-header{position:fixed;top:0;left:0;right:0;background:var(--black);padding:15px 0;z-index:1000;box-shadow:0 4px 20px rgba(0,0,0,.9);}
        .admin-logo{color:var(--yellow);font-size:1.9em;font-weight:900;text-decoration:none;}
        .nav-links a{color:#ccc;}.nav-links a:hover,.nav-links a.active{color:var(--yellow);}
        .btn-logout{background:var(--yellow);color:#000;padding:10px 25px;border-radius:50px;font-weight:bold;}

        /* ADD SPACE SO CONTENT DOESN'T HIDE UNDER FIXED NAVBAR */
        .admin-container{padding:120px 30px 50px;} /* ← THIS FIXES OVERLAP */

        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;margin-bottom:50px;}
        .card{background:var(--dark);padding:30px;border-radius:16px;text-align:center;box-shadow:0 10px 35px rgba(0,0,0,.7);transition:.4s;}
        .card:hover{transform:translateY(-12px);box-shadow:0 25px 50px rgba(255,193,7,.4);border:2px solid var(--yellow);}
        .card i{font-size:3.2em;color:var(--yellow);margin-bottom:12px;}
        .card h3{margin:10px 0;color:#aaa;font-size:1.1em;letter-spacing:1px;}
        .card p{font-size:2.4em;font-weight:900;color:var(--yellow);margin:0;} /* ← REDUCED SIZE */

        .chart-section{background:var(--dark);padding:40px;border-radius:16px;box-shadow:0 15px 50px rgba(0,0,0,.8);max-width:1100px;margin:0 auto;}
        .tab-buttons{display:flex;justify-content:center;gap:15px;margin-bottom:25px;flex-wrap:wrap;}
        .tab-btn{background:#333;color:#fff;padding:12px 32px;border:none;border-radius:50px;font-weight:bold;cursor:pointer;}
        .tab-btn.active{background:var(--yellow);color:#000;}
        .tab-btn:hover:not(.active){background:#555;}
        .chart-wrapper{position:relative;height:420px;width:100%;}
        .chart-title{text-align:center;font-size:2.1em;margin:0 0 30px;color:var(--yellow);font-weight:900;}
    </style>
</head>
<body class="admin-page">

<header class="admin-header">
    <nav class="admin-nav">
        <div class="nav-left">
            <a href="dashboard.php" class="admin-logo">Admin Panel</a>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="sales_report.php">Sales</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
</header>

<main class="admin-container">
    <div class="cards">
        <div class="card"><i class="fas fa-users"></i><h3>TOTAL USERS</h3><p><?=number_format($t_users)?></p></div>
        <div class="card"><i class="fas fa-box"></i><h3>TOTAL PRODUCTS</h3><p><?=number_format($t_products)?></p></div>
        <div class="card"><i class="fas fa-peso-sign"></i><h3>TOTAL SALES</h3><p>₱<?=number_format($t_sales,2)?></p></div>
    </div>

    <div class="chart-section">
        <div class="tab-buttons">
            <button class="tab-btn active" data-view="week">Weekly</button>
            <button class="tab-btn" data-view="month">Monthly</button>
            <button class="tab-btn" data-view="year">Yearly</button>
        </div>
        <h2 class="chart-title" id="chartTitle">Weekly Sales Revenue</h2>
        <div class="chart-wrapper">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</main>

<script>
const weekly  = {labels:<?=json_encode($weeks)?>, data:<?=json_encode($week_revenues)?>};
const monthly = {labels:<?=json_encode($months)?>, data:<?=json_encode($month_revenues)?>};
const yearly  = {labels:<?=json_encode($years)?>, data:<?=json_encode($year_revenues)?>};

let chart;
function render(view) {
    const data = view==='week'?weekly:view==='month'?monthly:yearly;
    const title = view==='week'?'Weekly Sales Revenue':view==='month'?'Monthly Sales Revenue':'Yearly Sales Revenue';
    document.getElementById('chartTitle').textContent = title;
    if(chart) chart.destroy();
    chart = new Chart(document.getElementById('salesChart'), {
        type:'line',
        data:{labels:data.labels, datasets:[{label:'Revenue', data:data.data,
            backgroundColor:'rgba(255,193,7,0.2)', borderColor:'#FFC107', borderWidth:4,
            pointBackgroundColor:'#FFC107', pointBorderColor:'#000', pointBorderWidth:3,
            pointRadius:8, pointHoverRadius:12, tension:0.4, fill:true}]},
        options:{responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true, grid:{color:'rgba(255,255,255,0.05)'}, ticks:{color:'#ccc', callback:v=>'₱'+v.toLocaleString()}},
                    x:{grid:{display:false}, ticks:{color:'#aaa'}}}}
    });
}
render('week');

document.querySelectorAll('.tab-btn').forEach(b=>b.addEventListener('click',function(){
    document.querySelectorAll('.tab-btn').forEach(x=>x.classList.remove('active'));
    this.classList.add('active');
    render(this.dataset.view);
}));
</script>
</body>
</html>