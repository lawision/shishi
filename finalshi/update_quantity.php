<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); exit();
}

$user_id = (int) $_SESSION['user']['user_id'];
$cart_id = isset($_POST['cart_id']) ? (int) $_POST['cart_id'] : 0;
$action = $_POST['action'] ?? '';

if (!$cart_id || !$action) {
    header("Location: cart.php");
    exit();
}

// Get existing cart row (ensure row belongs to user)
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    $stmt->close();
    $_SESSION['cart_message'] = "Cart item not found.";
    header("Location: cart.php");
    exit();
}
$row = $res->fetch_assoc();
$qty = (int) $row['quantity'];
$stmt->close();

if ($action === 'inc') {
    $qty++;
} elseif ($action === 'dec' && $qty > 1) {
    $qty--;
} else {
    // do nothing if trying to decrement below 1
    header("Location: cart.php");
    exit();
}

// Update DB
$u = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
$u->bind_param("iii", $qty, $cart_id, $user_id);
$u->execute();
$u->close();

header("Location: cart.php");
exit();
