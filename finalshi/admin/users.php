<?php
include 'admin_protect.php';
include '../connect.php';

// Fetch users
$res = $conn->query("
    SELECT user_id, first_name, last_name, email_address, created_at 
    FROM `user` 
    ORDER BY created_at DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Users - Admin</title>
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
  <h1>Users</h1>

  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Joined</th>
      </tr>
    </thead>
    <tbody>
      <?php while($u = $res->fetch_assoc()): ?>
      <tr>
        <td data-label="ID"><?= $u['user_id'] ?></td>
        <td data-label="Name"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
        <td data-label="Email"><?= htmlspecialchars($u['email_address']) ?></td>
        <td data-label="Joined"><?= $u['created_at'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>

</body>
</html>
