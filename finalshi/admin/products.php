<?php
// admin/products.php
include 'admin_protect.php';
include '../connect.php';

// Fetch products WITH category name
$result = $conn->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Products - Admin</title>
  <link rel="stylesheet" href="dashboard.css">
  <script src="admin_script.js" defer></script>
  <style>
    .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
    .category-badge { 
      padding: 4px 10px; 
      background: #e3f2fd; 
      color: #1976d2; 
      border-radius: 12px; 
      font-size: 0.85em; 
      font-weight: 500;
    }
    .no-category { color: #999; font-style: italic; }
  </style>
</head>
<body class="admin-page">

<header class="admin-header">
    <nav class="admin-nav">
        <div class="nav-left">
            <a href="dashboard.php" class="admin-logo">Admin Panel</a>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php" class="active">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="sales.php">Sales</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
</header>

<main class="admin-container">
  <h1>Products</h1>
  <div class="add-product-btn">
    <a href="product_add.php" class="small-btn">Add Product</a>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Brand</th>
        <th>Category</th>         <!-- NEW COLUMN -->
        <th>Price</th>
        <th>Stock</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($p = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td>
            <img src="../<?= htmlspecialchars($p['image']) ?>" 
                 alt="<?= htmlspecialchars($p['name']) ?>" 
                 class="product-img">
          </td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['brand']) ?></td>
          
          <!-- CATEGORY COLUMN -->
          <td>
            <?php if ($p['category_name']): ?>
              <span class="category-badge"><?= htmlspecialchars($p['category_name']) ?></span>
            <?php else: ?>
              <span class="no-category">Uncategorized</span>
            <?php endif; ?>
          </td>
          
          <td>₱<?= number_format($p['price'], 2) ?></td>
          <td><?= $p['quantity'] ?></td>
          <td><?= ucfirst(htmlspecialchars($p['status'])) ?></td>
          <td>
            <a href="product_edit.php?id=<?= $p['id'] ?>" class="small-btn">Edit</a>
            <form method="post" action="product_delete.php" style="display:inline" 
                  onsubmit="return confirm('Delete this product?')">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="small-btn danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>

</body>
</html>