<?php
session_start();
require 'db.php'; // Adjust path if needed

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty'])) {
        $product_id = intval($_POST['product_id']);
        $qty = intval($_POST['quantity']);
        $_SESSION['cart'][$product_id] = max(1, $qty);
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['remove_item'])) {
        $product_id = intval($_POST['product_id']);
        unset($_SESSION['cart'][$product_id]);
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f5f5f5;
      font-family: Arial;
    }

    .header {
      background-color: #ee4d2d;
      color: white;
      padding: 15px;
      text-align: left;
      position: relative;
    }

    .header .message-icon {
      position: absolute;
      right: 15px;
      top: 5px;
      font-size: 20px;
      cursor: pointer;
      color: white;
    }

    .cart-container {
      max-width: 700px;
      margin: 20px auto;
      background: #fff;
      border-radius: 10px;
      padding: 20px;
    }

    .cart-item {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #ddd;
      padding: 10px 0;
    }

    .cart-item img {
      width: 70px;
      height: 70px;
      object-fit: contain;
      margin-right: 15px;
      border-radius: 10px;
      background: #fff;
    }

    .item-info {
      flex: 1;
    }

    .item-name {
      font-weight: bold;
      font-size: 16px;
    }

    .stock {
      font-size: 12px;
      color: gray;
    }

    .qty-controls {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 5px;
    }

    .qty-controls input {
      width: 40px;
      text-align: center;
    }

    .delete-btn {
      background: none;
      border: none;
      color: red;
      font-size: 16px;
      cursor: pointer;
    }

    .checkout-box {
      margin-top: 10px;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 10px;
      padding-bottom: 15px;
    }

    .total-price {
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }

    .checkout-box button {
      background: #ee4d2d;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="cart-container">
    <h4>My Cart</h4>

    <?php if (count($cart) > 0): ?>
    <form method="post">
      <?php
      $total = 0;
      foreach ($cart as $product_id => $qty):
        if (!is_numeric($product_id) || $qty <= 0) continue;

        $product_id = intval($product_id);
        $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
        $product = mysqli_fetch_assoc($result);
        if (!$product) continue;

        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
      ?>
      <div class="cart-item">
        <img src="images/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
        <div class="item-info">
          <div class="item-name"><?= htmlspecialchars($product['product_name']) ?></div>
          <div class="price text-muted">₱<?= number_format($product['price'], 2) ?> x <?= $qty ?></div>
          <div class="stock">Stock: <?= $product['stock_quantity'] ?></div>
          <div class="qty-controls">
            <form method="post" style="display: flex;">
              <input type="hidden" name="product_id" value="<?= $product_id ?>">
              <button type="submit" name="update_qty" onclick="this.parentElement.querySelector('input[name=quantity]').stepDown();">-</button>
              <input type="number" name="quantity" value="<?= $qty ?>" min="1">
              <button type="submit" name="update_qty" onclick="this.parentElement.querySelector('input[name=quantity]').stepUp();">+</button>
            </form>
          </div>
        </div>
        <form method="post" style="margin-left: 10px;">
          <input type="hidden" name="product_id" value="<?= $product_id ?>">
          <button type="submit" name="remove_item" class="delete-btn">&times;</button>
        </form>
      </div>
      <?php endforeach; ?>
    </form>

    <div class="checkout-box">
      <div class="total-price">Total: ₱<?= number_format($total, 2) ?></div>
      <form action="checkout.php" method="POST">
        <button type="submit">Buy</button>
      </form>
    </div>

    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>
</body>
</html>
