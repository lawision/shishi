<?php
session_start();
include 'connect.php';

// Must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {

    $product_id = intval($_POST['product_id']);

    // 1. Check if product exists and has stock
    $pcheck = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
    $pcheck->bind_param("i", $product_id);
    $pcheck->execute();
    $product = $pcheck->get_result()->fetch_assoc();

    if (!$product) {
        die("Invalid product.");
    }

    if ($product['quantity'] <= 0) {
        header("Location: viewproduct.php?id=$product_id&error=nostock");
        exit();
    }

    // 2. Check if item already in cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;

    if ($exists) {
        // Already added — redirect with message
        header("Location: cart.php?msg=exists");
        exit();
    }

    // 3. Insert new item with quantity = 1
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    header("Location: cart.php?msg=added");
    exit();
}
?>
