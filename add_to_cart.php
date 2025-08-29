<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$product_id || !is_numeric($product_id)) {
  echo json_encode(['success' => false]);
  exit;
}

$_SESSION['cart'][$product_id] = $quantity;

// Recalculate cart count
$cart_count = 0;
foreach ($_SESSION['cart'] as $qty) {
  $cart_count += $qty;
}

echo json_encode(['success' => true, 'cart_count' => $cart_count]);
