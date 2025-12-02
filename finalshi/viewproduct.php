<?php
include 'connect.php';
session_start();

$isLoggedIn = isset($_SESSION['user']);
$currentUser = $isLoggedIn ? $_SESSION['user'] : null;

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? htmlspecialchars($product['name']) : 'Product Not Found' ?> - Thriftoes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/index.css"> <!-- Using the same CSS as index.php -->
    <style>
        /* Additional styles specific to product view */
        .back-arrow {
            position: absolute;
            top: 100px;
            left: 40px;
            font-size: 2rem;
            color: #C9A227;
            text-decoration: none;
            background: rgba(0,0,0,0.7);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 99;
            box-shadow: 0 4px 15px rgba(201,162,39,0.3);
        }
        .back-arrow:hover {
            background: #C9A227;
            color: #000;
            transform: scale(1.1);
        }

        #product-details {
            padding: 120px 40px 80px;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 80vh;
        }

        .product-detail-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            background: #1a1a1a;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(201,162,39,0.15);
            border: 1px solid #333;
        }

        .product-detail-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 20px 0 0 20px;
        }

        .product-info {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #F8F8F4;
        }

        .product-info h2 {
            font-size: 2.8rem;
            color: #C9A227;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .product-info p {
            font-size: 1.2rem;
            margin: 12px 0;
            color: #ddd;
        }

        .product-info strong {
            color: #C9A227;
        }

        .price {
            font-size: 2.5rem !important;
            font-weight: bold;
            color: #C9A227 !important;
            margin: 20px 0;
        }

        .stock {
            font-size: 1.1rem;
            padding: 8px 15px;
            background: <?= $product && $product['quantity'] > 0 ? '#004d00' : '#330000' ?>;
            color: <?= $product && $product['quantity'] > 0 ? '#00ff00' : '#ff4444' ?>;
            display: inline-block;
            border-radius: 30px;
            margin: 15px 0;
            font-weight: bold;
        }

        .add-to-cart {
            background: #C9A227;
            color: #000;
            border: none;
            padding: 16px 40px;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(201,162,39,0.4);
        }

        .add-to-cart:hover {
            background: #e6c040;
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(201,162,39,0.6);
        }

        .add-to-cart:disabled {
            background: #444;
            color: #888;
            cursor: not-allowed;
            box-shadow: none;
        }

        .login-prompt {
            text-align: center;
            padding: 40px;
            font-size: 1.3rem;
            color: #aaa;
        }

        .login-prompt a {
            color: #C9A227;
            text-decoration: underline;
        }

        @media (max-width: 992px) {
            .product-detail-card {
                grid-template-columns: 1fr;
            }
            .product-detail-card img {
                border-radius: 20px 20px 0 0;
                max-height: 500px;
            }
            .back-arrow {
                top: 90px;
                left: 20px;
            }
        }

        @media (max-width: 768px) {
            .product-info h2 { font-size: 2.2rem; }
            .product-info { padding: 30px; }
            #product-details { padding: 100px 20px 60px; }
        }
    </style>
</head>
<body>

<!-- SAME NAVBAR AS INDEX.PHP -->
<header>
    <a href="index.php">
        <img src="Logo.png" class="logo" alt="Thriftoes Logo" />
    </a>

    <nav>
        <ul id="navbar">
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php" class="active">Shop</a></li>
            <li>
                <a href="#" aria-label="Shopping Cart" id="cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
            </li>

            <!-- MENU OPTIONS CHANGE IF LOGGED IN -->
            <li class="submenu">
                <a href="#" aria-label="More Options">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </a>
                <ul class="submenu-options">
                    <?php if (!$isLoggedIn): ?>
                        <li><a href="signup.php">Sign Up</a></li>
                        <li><a href="login.php" id="login">Log In</a></li>
                    <?php else: ?>
                        <li><a href="profile.php">My Profile</a></li>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="index.php?logout=1">Log Out</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<!-- Back to Shop Button -->
<a href="shop.php" class="back-arrow" title="Back to Shop">
    <i class="fa fa-arrow-left"></i>
</a>

<section id="product-details">
    <?php if ($product): ?>
        <div class="product-detail-card">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

            <div class="product-info">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?></p>
                <?php if (!empty($product['size'])): ?>
                    <p><strong>Size:</strong> <?= htmlspecialchars($product['size']) ?></p>
                <?php endif; ?>
                <?php if (!empty($product['color'])): ?>
                    <p><strong>Color:</strong> <?= htmlspecialchars($product['color']) ?></p>
                <?php endif; ?>
                <p class="price">₱<?= number_format($product['price'], 2) ?></p>
                <p class="stock">
                    <?= $product['quantity'] > 0 ? "In Stock ({$product['quantity']} left)" : "Out of Stock" ?>
                </p>
                <?php if (!empty($product['description'])): ?>
                    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <?php if ($product['quantity'] > 0): ?>
                        <form method="POST" action="addtocart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="add-to-cart">
                                Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="add-to-cart" disabled>Out of Stock</button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="login-prompt">
                        Please <a href="login.php">log in</a> to add this item to your cart.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align:center;padding:100px;color:#888;">
            <h2>Product Not Found</h2>
            <p>The item you're looking for doesn't exist or has been removed.</p>
            <a href="shop.php" style="color:#C9A227;font-size:1.3rem;">Back to Shop</a>
        </div>
    <?php endif; ?>
</section>

<!-- SAME CART SCRIPT AS INDEX -->
<script>
document.getElementById("cart").addEventListener("click", function(e) {
    <?php if (!$isLoggedIn): ?>
        e.preventDefault();
        alert("Please log in first to view your cart.");
        window.location.href = "login.php";
    <?php else: ?>
        window.location.href = "cart.php";
    <?php endif; ?>
});
</script>

</body>
</html>