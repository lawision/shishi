<?php
include 'admin_protect.php';
include '../connect.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: products.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $brand       = trim($_POST['brand']);
    $size        = trim($_POST['size']);
    $color       = trim($_POST['color']);
    $price       = (float)$_POST['price'];
    $quantity    = (int)$_POST['quantity'];
    $description = trim($_POST['description']);
    $status      = $_POST['status'];
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    $imgPath = $prod['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $targetDir = '../uploads/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $target = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Optional: delete old image
                if ($prod['image'] !== 'img/default-helmet.jpg' && file_exists('../' . $prod['image'])) {
                    unlink('../' . $prod['image']);
                }
                $imgPath = 'uploads/' . $fileName;
            }
        } else {
            $message = "Only JPG, PNG, WebP allowed.";
        }
    }

    if (!$message) {
        $u = $conn->prepare("
            UPDATE products SET 
            name=?, brand=?, size=?, color=?, price=?, quantity=?, image=?, description=?, category_id=?, status=?
            WHERE id=?
        ");
        $u->bind_param("ssssdissssi", $name, $brand, $size, $color, $price, $quantity, $imgPath, $description, $category_id, $status, $id);
        $u->execute();
        header("Location: products.php?updated=1");
        exit;
    }
}

$cats = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin Panel</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root{
            --black:#000;
            --yellow:#FFC107;
            --dark:#222;
        }
        body{background:#000;color:#fff;font-family:'Segoe UI',sans-serif;margin:0;}
        
        /* SAME FIXED NAVBAR AS ALL ADMIN PAGES */
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

        .admin-container{
            padding:120px 30px 80px;
            min-height:100vh;
        }

        .page-wrapper{
            max-width:1000px;
            margin:0 auto;
        }
        .page-title{
            text-align:center;
            font-size:2.8em;
            color:var(--yellow);
            margin:0 0 40px;
            font-weight:900;
            text-shadow:0 3px 10px rgba(255,193,7,.5);
        }

        .form-container{
            background:var(--dark);
            padding:40px;
            border-radius:16px;
            box-shadow:0 15px 50px rgba(0,0,0,.8);
        }

        .form-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));
            gap:28px;
        }

        label{
            display:block;
            color:#ccc;
            font-weight:600;
            margin-bottom:8px;
        }
        input, select, textarea{
            width:100%;
            padding:14px;
            background:#333;
            border:1px solid #444;
            border-radius:10px;
            color:#fff;
            font-size:1em;
            transition:.3s;
        }
        input:focus, select:focus, textarea:focus{
            outline:none;
            border-color:var(--yellow);
            box-shadow:0 0 0 3px rgba(255,193,7,.2);
        }
        textarea{ min-height:130px; resize:vertical; }

        .current-img{
            max-width:200px;
            border-radius:12px;
            margin-top:8px;
            border:2px solid #444;
        }

        .btn-group{
            grid-column:1/-1;
            text-align:center;
            margin-top:30px;
            gap:20px;
            display:flex;
            justify-content:center;
        }
        .btn{
            background:var(--yellow);
            color:#000;
            padding:16px 50px;
            border:none;
            border-radius:50px;
            font-weight:bold;
            font-size:1.1em;
            cursor:pointer;
            transition:.4s;
        }
        .btn:hover{background:#e6ac00;transform:translateY(-4px);}
        .btn.ghost{
            background:transparent;
            color:var(--yellow);
            border:2px solid var(--yellow);
        }
        .btn.ghost:hover{
            background:var(--yellow);
            color:#000;
        }

        .message.error{
            background:#440000;
            color:#ff6b6b;
            padding:15px;
            border-radius:10px;
            text-align:center;
            margin-bottom:25px;
            font-weight:bold;
        }
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
        <h1 class="page-title">EDIT PRODUCT</h1>

        <?php if($message): ?>
            <div class="message error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" enctype="multipart/form-data" class="form-grid">

                <div>
                    <label>Product Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($prod['name']) ?>" required>
                </div>

                <div>
                    <label>Brand *</label>
                    <input type="text" name="brand" value="<?= htmlspecialchars($prod['brand']) ?>" required>
                </div>

                <div>
                    <label>Size</label>
                    <input type="text" name="size" value="<?= htmlspecialchars($prod['size']) ?>">
                </div>

                <div>
                    <label>Color</label>
                    <input type="text" name="color" value="<?= htmlspecialchars($prod['color']) ?>">
                </div>

                <div>
                    <label>Price (₱) *</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?= $prod['price'] ?>" required>
                </div>

                <div>
                    <label>Stock Quantity *</label>
                    <input type="number" name="quantity" min="0" value="<?= $prod['quantity'] ?>" required>
                </div>

                <div>
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">Uncategorized</option>
                        <?php foreach($cats as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($prod['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="available" <?= $prod['status']=='available'?'selected':'' ?>>Available</option>
                        <option value="sold" <?= $prod['status']=='sold'?'selected':'' ?>>Sold Out</option>
                    </select>
                </div>

                <div>
                    <label>Current Image</label>
                    <img src="../<?= htmlspecialchars($prod['image']) ?>" alt="Current" class="current-img">
                </div>

                <div>
                    <label>Change Image</label>
                    <input type="file" name="image" accept="image/*">
                    <small style="color:#888;">JPG, PNG, WebP only</small>
                </div>

                <div style="grid-column:1/-1;">
                    <label>Description</label>
                    <textarea name="description" rows="6"><?= htmlspecialchars($prod['description']) ?></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn">Update Product</button>
                    <a href="products.php" class="btn ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

</body>
</html>