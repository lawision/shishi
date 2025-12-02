<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$user_id = (int) $_SESSION['user']['user_id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$_SESSION['cart_message'] = "Cart cleared.";
header("Location: cart.php");
exit();
