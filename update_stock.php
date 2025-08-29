<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['product_id'];
  $newQty = $_POST['new_quantity'];

  $query = "UPDATE products SET stock_quantity = $newQty WHERE product_id = $id";
  if ($conn->query($query)) {
    header("Location: admin.php");
    exit();
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
