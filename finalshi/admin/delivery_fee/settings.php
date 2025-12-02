<?php
include 'connect.php';
$price_per_km = $conn->query("SELECT value FROM settings WHERE key_name='price_per_km'")->fetch_assoc()['value'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $new_price = $_POST['price'];
    $stmt = $conn->prepare("UPDATE settings SET value=? WHERE key_name='price_per_km'");
    $stmt->bind_param("d",$new_price);
    $stmt->execute();
    header("Location: index.php");
}
?>
<form method="post">
    <h2>Shipping Price per km</h2>
    <input name="price" type="number" step="0.01" value="<?= $price_per_km ?>" required><br>
    <button type="submit">Update</button>
</form>
<a href="index.php">Back</a>
