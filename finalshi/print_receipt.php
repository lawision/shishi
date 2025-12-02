<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit(); }
$user_id = (int) $_SESSION['user']['user_id'];
$sale_id = isset($_GET['sale_id']) ? (int) $_GET['sale_id'] : 0;
if ($sale_id <= 0) { echo "Invalid sale id"; exit(); }

// fetch sale
$stmt = $conn->prepare("SELECT * FROM sales WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $sale_id, $user_id);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$sale) { echo "Order not found."; exit(); }

// items
$stmt = $conn->prepare("SELECT sp.quantity, sp.price_at_purchase, p.name, p.brand, p.image FROM sales_products sp JOIN products p ON sp.product_id = p.id WHERE sp.sale_id = ?");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Receipt #<?= $sale_id ?></title>
<style>
  body{font-family:Arial; padding:20px; color:#222}
  .receipt{max-width:700px;margin:0 auto}
  .h{display:flex;justify-content:space-between;align-items:center}
  table{width:100%;border-collapse:collapse;margin-top:15px}
  table th, table td{padding:8px;border-bottom:1px solid #eee;text-align:left}
  .tot{font-weight:bold}
  .print-btn{padding:8px 12px;background:#0fb9a8;color:#fff;border:none;border-radius:6px;cursor:pointer}
</style>
</head>
<body>
<div class="receipt">
  <div class="h">
    <div>
      <h2>ThrifToes</h2>
      <div>Order #<?= $sale_id ?></div>
      <div>Date: <?= $sale['order_date'] ?></div>
    </div>
    <div>
      <button onclick="window.print()" class="print-btn">Print</button>
    </div>
  </div>

  <table>
    <thead><tr><th>Item</th><th>Brand</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
    <tbody>
      <?php $subtotal=0; foreach($items as $it): $line=$it['quantity']*$it['price_at_purchase']; $subtotal += $line; ?>
        <tr>
          <td><?= htmlspecialchars($it['name']) ?></td>
          <td><?= htmlspecialchars($it['brand']) ?></td>
          <td><?= (int)$it['quantity'] ?></td>
          <td>₱<?= number_format($it['price_at_purchase'],2) ?></td>
          <td>₱<?= number_format($line,2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="text-align:right;margin-top:12px">
    <div>Subtotal: ₱<?= number_format($subtotal,2) ?></div>
    <div>Delivery: ₱100.00</div>
    <div class="tot">Total: ₱<?= number_format($sale['total_amount'],2) ?></div>
  </div>
</div>
</body>
</html>
