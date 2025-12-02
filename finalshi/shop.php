<?php
session_start();
include 'connect.php';

$isLoggedIn = isset($_SESSION['user']);

// Filters
$filter   = trim($_GET['filter'] ?? '');
$category = $_GET['category'] ?? '';

// Build WHERE conditions
$where = [];
$params = [];
$types = "";

if ($filter !== '') {
    $where[] = "(p.name LIKE ? OR p.brand LIKE ? OR p.size LIKE ? OR p.color LIKE ?)";
    $like = "%$filter%";
    $params = array_merge($params, [$like, $like, $like, $like]);
    $types .= "ssss";
}

if ($category !== '' && ctype_digit((string)$category)) {
    $where[] = "p.category_id = ?";
    $params[] = (int)$category;
    $types .= "i";
}

// Base query
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.created_at DESC";

// Get all categories for dropdown
$catResult = $conn->query("SELECT id, name FROM categories ORDER BY name");
$categories = $catResult->fetch_all(MYSQLI_ASSOC);

// Execute product query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HHH - Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/shop.css">
    <style>
        .filters { margin: 30px 0; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .filters input, .filters select { padding: 10px 14px; font-size: 16px; border: 1px solid #ddd; border-radius: 6px; }
        .filters button { background: #0fb9a8; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
        .filters .clear { background: #f1f1f1; color: #333; text-decoration: none; padding: 10px 16px; border-radius: 6px; }
        .category-tag { display: block; font-size: 0.85em; color: #0fb9a8; margin: 4px 0; }
    </style>
</head>
<body>

<header>
    <a href="<?= $isLoggedIn ? 'index.php' : 'guestindex.php'; ?>">
        <img src="img/logo.png" class="logo" alt="Logo">
    </a>
    <nav>
        <ul id="navbar">
            <li><a href="#" id="home-link">Home</a></li>
            <li><a href="shop.php" class="active">Shop</a></li>
            <li><a href="<?= $isLoggedIn ? 'cart.php' : 'login.php'; ?>"><i class="fa-solid fa-cart-shopping"></i></a></li>
            <li class="submenu">
                <a href="#"><i class="fa fa-bars"></i></a>
                <ul class="submenu-options">
                    <?php if (!$isLoggedIn): ?>
                        <li><a href="signup.php">Sign Up</a></li>
                        <li><a href="login.php">Log In</a></li>
                    <?php else: ?>
                        <li><a href="toreceive.php">To Receive</a></li>
                        <li><a href="purchaseHistory.php">Purchase History</a></li>
                        <li><a href="index.php?logout=true">Log Out</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<!-- FILTER BAR -->
<div class="filters">
    <form method="GET">
        <input type="text" name="filter" placeholder="Search brand, model, size, color..." value="<?= htmlspecialchars($filter) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Apply Filter</button>
        <?php if ($filter !== '' || $category !== ''): ?>
            <a href="shop.php" class="clear">Clear Filters</a>
        <?php endif; ?>
    </form>
</div>

<section id="products" class="section1">
    <h2>Helmet Collection
        <?php if ($category): ?>
            — <?= htmlspecialchars(array_column($categories, 'name', 'id')[$category] ?? 'Category') ?>
        <?php endif; ?>
    </h2>
    <div class="con">
        <?php if (empty($products)): ?>
            <p>No helmets found matching your search.</p>
        <?php else: ?>
            <?php foreach ($products as $product):
                $out_of_stock = $product['quantity'] <= 0; ?>
                <div class="product-card">
                    <a href="viewproduct.php?id=<?= $product['id'] ?>">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                    <div class="des <?= $out_of_stock ? 'sold' : '' ?>">
                        <span><?= htmlspecialchars($product['brand']) ?></span>
                        <?php if (!empty($product['category_name'])): ?>
                            <small class="category-tag"><?= htmlspecialchars($product['category_name']) ?></small>
                        <?php endif; ?>
                        <h5><?= htmlspecialchars($product['name']) ?></h5>
                        <h5>Size: <?= htmlspecialchars($product['size']) ?></h5>
                        <h5>Color: <?= htmlspecialchars($product['color']) ?></h5>
                        <h5><b>Stock: <?= $product['quantity'] ?></b></h5>
                        <h4>₱<?= number_format($product['price'], 2) ?></h4>
                    </div>
                    <form method="POST" action="addtocart.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="add-to-cart" <?= $out_of_stock ? 'disabled' : '' ?>>
                            <?= $out_of_stock ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer><p>&copy; 2024 ThrifToes. All rights reserved.</p></footer>

<script>
    document.getElementById('home-link').addEventListener('click', function(e) {
        e.preventDefault();
        location.href = <?= $isLoggedIn ? "'index.php'" : "'guestindex.php'" ?>;
    });
</script>
</body>
</html>