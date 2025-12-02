<?php
include 'admin_protect.php';
include '../connect.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $brand       = trim($_POST['brand']);
    $size        = trim($_POST['size']);
    $color       = trim($_POST['color']);
    $price       = (float)$_POST['price'];
    $quantity    = (int)$_POST['quantity'];
    $description = trim($_POST['description']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    // Image upload
    $imgPath = 'img/default-helmet.jpg'; // default image
    if (!empty($_FILES['image']['name'])) {
        $fileError = $_FILES['image']['error'];
        if ($fileError !== UPLOAD_ERR_OK) {
            $message = "Upload error code: $fileError";
        } else {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $targetDir = '../uploads/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $target = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $imgPath = 'uploads/' . $fileName;
                } else {
                    $message = "Failed to move uploaded file. Check folder permissions.";
                }
            } else {
                $message = "Invalid image format. Only JPG, JPEG, PNG, WebP allowed.";
            }
        }
    }

    // Insert into database if no errors
    if (!$message) {
        $stmt = $conn->prepare("
            INSERT INTO products 
            (name, brand, size, color, price, quantity, image, description, category_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', NOW())
        ");
        // FIXED bind_param: 9 params match the SQL placeholders
        $stmt->bind_param("sssdisiss", $name, $brand, $size, $color, $price, $quantity, $imgPath, $description, $category_id);
        if ($stmt->execute()) {
            header("Location: products.php?added=1");
            exit;
        } else {
            $message = "Database error: " . $stmt->error;
        }
    }
}

// Load categories
$cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product - Admin</title>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<style>
:root{--black:#000;--yellow:#FFC107;--dark:#222;}
body{background:#000;color:#fff;font-family:'Segoe UI',sans-serif;margin:0;}
.admin-header{position:fixed;top:0;left:0;right:0;background:var(--black);padding:15px 0;z-index:1000;box-shadow:0 4px 20px rgba(0,0,0,0.9);}
.admin-logo{color:var(--yellow);font-size:1.9em;font-weight:900;text-decoration:none;}
.nav-links a{color:#ccc;transition:.3s;}
.nav-links a:hover,.nav-links a.active{color:var(--yellow);}
.btn-logout{background:var(--yellow);color:#000;padding:10px 25px;border-radius:50px;font-weight:bold;}
.admin-container{padding:120px 30px 80px;min-height:100vh;}
.page-wrapper{max-width:900px;margin:0 auto;}
.page-title{text-align:center;font-size:2.8em;color:var(--yellow);margin:0 0 40px;font-weight:900;text-shadow:0 3px 10px rgba(255,193,7,.5);}
.form-container{background:var(--dark);padding:40px;border-radius:16px;box-shadow:0 15px 50px rgba(0,0,0,.8);}
.form-grid{display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:25px;}
label{display:block;color:#ccc;font-weight:600;margin-bottom:8px;}
input, select, textarea{width:100%;padding:14px;background:#333;border:1px solid #444;border-radius:10px;color:#fff;font-size:1em;transition:.3s;}
input:focus, select:focus, textarea:focus{outline:none;border-color:var(--yellow);box-shadow:0 0 0 3px rgba(255,193,7,.2);}
textarea{min-height:120px;resize:vertical;}
.file-input{padding:10px 0;}
.btn-group{grid-column:1/-1;text-align:center;margin-top:20px;}
.btn{background:var(--yellow);color:#000;padding:14px 40px;border:none;border-radius:50px;font-weight:bold;font-size:1.1em;cursor:pointer;transition:.3s;margin:0 10px;}
.btn:hover{background:#e6ac00;}
.btn.ghost{background:transparent;color:var(--yellow);border:2px solid var(--yellow);}
.btn.ghost:hover{background:var(--yellow);color:#000;}
.message{padding:15px;border-radius:10px;margin-bottom:25px;text-align:center;font-weight:bold;}
.error{background:#440000;color:#ff6b6b;}
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
<li><a href="sales_report.php">Sales</a></li>
</ul>
</div>
<div class="nav-right">
<a href="logout.php" class="btn-logout">Logout</a>
</div>
</nav>
</header>

<main class="admin-container">
<div class="page-wrapper">
<h1 class="page-title">ADD NEW PRODUCT</h1>

<?php if($message): ?>
<div class="message error"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="form-container">
<form method="post" enctype="multipart/form-data" class="form-grid">
<div>
<label>Product Name *</label>
<input type="text" name="name" required>
</div>
<div>
<label>Brand *</label>
<input type="text" name="brand" required>
</div>
<div>
<label>Size</label>
<input type="text" name="size" placeholder="e.g. M, L, XL">
</div>
<div>
<label>Color</label>
<input type="text" name="color" placeholder="e.g. Black, Red">
</div>
<div>
<label>Price (₱) *</label>
<input type="number" name="price" step="0.01" min="0" required>
</div>
<div>
<label>Stock Quantity *</label>
<input type="number" name="quantity" min="0" value="1" required>
</div>
<div>
<label>Category</label>
<select name="category_id">
<option value="">Uncategorized</option>
<?php foreach($cats as $c): ?>
<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
<?php endforeach; ?>
</select>
</div>
<div>
<label>Product Image</label>
<input type="file" name="image" accept="image/*" class="file-input">
<small style="color:#888;">JPG, JPEG, PNG, WebP allowed</small>
</div>
<div style="grid-column:1/-1;">
<label>Description</label>
<textarea name="description" rows="5" placeholder="Describe the product..."></textarea>
</div>

<div class="btn-group">
<button type="submit" class="btn">Add Product</button>
<a href="products.php" class="btn ghost">Cancel</a>
</div>
</form>
</div>
</div>
</main>

</body>
</html>
