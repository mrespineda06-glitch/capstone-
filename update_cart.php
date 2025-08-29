<?php
session_start();

$product_id = $_POST['product_id'];
$action = $_POST['action'];

if (!isset($_SESSION['cart'][$product_id])) {
  $_SESSION['cart'][$product_id] = 1;
}

if ($action == "plus") {
  $_SESSION['cart'][$product_id]++;
} elseif ($action == "minus" && $_SESSION['cart'][$product_id] > 1) {
  $_SESSION['cart'][$product_id]--;
}

header("Location: cart.php");
exit();
