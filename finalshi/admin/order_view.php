<?php
include 'admin_protect.php';
include '../connect.php';
$sale_id = isset($_GET['sale_id'])?(int)$_GET['sale_id']:0;
if (!$sale_id) { header("Location: orders.php"); exit(); }
$stmt = $conn->prepare("SELECT s.*, u.first_name, u.last_name, u.email_address FROM sales s JOIN `user` u ON s.user_id = u.user_id WHERE s.id = ?");
$stmt->bind_param("i",$sale_id); $stmt->execute(); $sale = $stmt->get_result()->fetch_assoc(); $stmt->close();
$stmt2 = $conn->prepare("SELECT sp.quantity, sp.price_at_purchase, p.* FROM sales_products sp JOIN products p ON sp.product_id = p.id WHERE sp.sale_id = ?");
$stmt2->bind_param("i",$sale_id); $stmt2->execute(); $items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC); $stmt2->close();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Order #<?=$sale_id?></title><link rel="stylesheet" href="admin_style.css"></head><body class="admin-page">
<header class="admin-header"><a class="logo" href="index.php">Admin</a></header>
<main class="admin-container">
  <h1>Order #<?=$sale_id?></h1>
  <div class="order-meta">
    <p><strong>Customer:</strong> <?=htmlspecialchars($sale['first_name'].' '.$sale['last_name'])?></p>
    <p><strong>Email:</strong> <?=htmlspecialchars($sale['email_address'])?></p>
    <p><strong>Date:</strong> <?=$sale['order_date']?></p>
    <p><strong>Status:</strong> <?=$sale['status']?></p>
  </div>
  <h2>Items</h2>
  <table class="admin-table"><thead><tr><th>Image</th><th>Name</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>
  <?php $sum=0; foreach($items as $it): $sub=$it['quantity']*$it['price_at_purchase']; $sum += $sub; ?>
    <tr>
      <td><img src="../<?=htmlspecialchars($it['image'])?>" style="width:70px"></td>
      <td><?=htmlspecialchars($it['name'])?></td>
      <td><?= (int)$it['quantity'] ?></td>
      <td>₱<?= number_format($it['price_at_purchase'],2) ?></td>
      <td>₱<?= number_format($sub,2) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody></table>
  <div class="order-total">Total: ₱<?= number_format($sale['total_amount'],2) ?></div>
  <a href="orders.php" class="btn">Back to Orders</a>
</main></body></html>
