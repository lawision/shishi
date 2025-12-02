<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$user_id = (int) $_SESSION['user']['user_id'];
$cart_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($cart_id <= 0) { header("Location: cart.php"); exit(); }

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: cart.php");
exit();
