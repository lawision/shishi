<?php
include 'admin_protect.php';
include '../connect.php';

// Handle status change via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sale_id'], $_POST['status'])) {
    $sid = (int)$_POST['sale_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE sales SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $sid);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}

// Fetch orders
$res = $conn->query("
    SELECT s.*, u.first_name, u.last_name 
    FROM sales s 
    JOIN `user` u ON s.user_id = u.user_id 
    ORDER BY s.order_date DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Orders - Admin</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body class="admin-page">

<!-- ===================== NAVBAR ===================== -->
<header class="admin-header">
    <nav class="admin-nav">
        <div class="nav-left">
            <a href="dashboard.php" class="admin-logo">Admin Panel</a>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">Dashboard</a></li>
                <li><a href="products.php" class="<?= basename($_SERVER['PHP_SELF'])=='products.php'?'active':'' ?>">Products</a></li>
                <li><a href="orders.php" class="<?= basename($_SERVER['PHP_SELF'])=='orders.php'?'active':'' ?>">Orders</a></li>
                <li><a href="users.php" class="<?= basename($_SERVER['PHP_SELF'])=='users.php'?'active':'' ?>">Users</a></li>
                <li><a href="sales.php" class="<?= basename($_SERVER['PHP_SELF'])=='sales.php'?'active':'' ?>">Sales</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
</header>

<!-- ===================== MAIN CONTENT ===================== -->
<main class="admin-container">
  <h1>Orders</h1>

  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Total</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($o = $res->fetch_assoc()): ?>
        <tr>
          <td data-label="ID">#<?= $o['id'] ?></td>
          <td data-label="User"><?= htmlspecialchars($o['first_name'].' '.$o['last_name']) ?></td>
          <td data-label="Total">₱<?= number_format($o['total_amount'],2) ?></td>
          <td data-label="Date"><?= $o['order_date'] ?></td>
          <td data-label="Status"><?= htmlspecialchars($o['status']) ?></td>
          <td data-label="Action">
            <a href="order_view.php?sale_id=<?= $o['id'] ?>" class="small-btn">View</a>
            <form method="post" style="display:inline">
              <input type="hidden" name="sale_id" value="<?= $o['id'] ?>">
              <select name="status">
                <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $o['status']===$s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="small-btn">Update</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>

</body>
</html>
