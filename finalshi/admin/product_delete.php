<?php
// admin/product_delete.php
include 'admin_protect.php';
include '../connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}
header("Location: products.php"); exit();
