<?php
session_start();
include 'db.php';

// Cart count for badge
$cart_count = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $qty) {
    $cart_count += $qty;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Home - Vending Machine</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
      font-family: Arial, sans-serif;
      font-size: 12px;
    }

    body {
      font-size: 12px;
      margin: 0;
      background-color: #f5f5f5;
      padding-bottom: 70px; /* Space for bottom nav */
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
    .fas-fa-headset {
      font-size: 50px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 10px;
      padding: 20px;
    }


    .product-card {
      background-color: white;
      padding: 12px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
      position: relative;
    }

    .product-top {
      position: relative;
      margin-bottom: 10px;
    }

    .product-img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
    }

    .add-to-cart-btn {
      position: absolute;
      top: 3px;
      right: 3px;
      background:transparent;
      border: none;
      cursor: pointer;
      padding: 6px;
      border-radius: 50%;
      color: white;
    }

    .add-to-cart-btn img {
      width: 18px;
      height: 18px;
    }

    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: white;
      border-top: 1px solid #ccc;
      display: flex;
      justify-content: space-around;
      align-items: center;
      height: 50px;
      z-index: 1000;
    }

    .nav-item {
      text-align: center;
      font-size: 10px;
      color: #444;
      text-decoration: none;
    }

    .nav-item i {
      font-size: 20px;
      display: block;
      margin-bottom: 2px;
    }

    .cart {
      position: relative;
    }

    .cart-count {
      position: absolute;
      top: -8px;
      right: -10px;
      background-color: red;
      color: white;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 50%;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <h1>VENDO</h1>
    <div class="message-icon" onclick="window.location.href='help_center.php';">
      <i class="fas fa-headset"></i>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="product-grid">
    <?php
    $query = "SELECT * FROM products WHERE status = 'available' AND stock_quantity > 0";
    $result = $conn->query($query);

    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
    <div class="product-card">
      <div class="product-top">
        <img src="images/<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" class="product-img">
        <button class="add-to-cart-btn" onclick="addToCart(<?php echo $row['product_id']; ?>)">
  <img src="https://cdn-icons-png.flaticon.com/512/833/833314.png" alt="Add to cart">
</button>

      </div>
      <h4><?php echo htmlspecialchars($row['product_name']); ?></h4>
       <p>Stocks: <?php echo $row['stock_quantity']; ?></p>
      <p>₱<?php echo $row['price']; ?></p>
    </div>
    <?php endwhile; else: ?>
      <p style="padding: 1rem;">No available products found.</p>
    <?php endif; ?>
  </div>

  <!-- Bottom Navigation -->
  <div class="bottom-nav">
    <a href="home.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="cart.php" class="nav-item cart">
      <i class="fas fa-shopping-cart"></i>
      <?php if ($cart_count > 0): ?>
        <span class="cart-count"><?php echo $cart_count; ?></span>
      <?php endif; ?>
      <span>Cart</span>
    </a>
    <a href="me.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Me</span>
    </a>
  </div>
  <script>
function addToCart(productId) {
  fetch('add_to_cart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'product_id=' + encodeURIComponent(productId) + '&quantity=1'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      updateCartCount(data.cart_count);
    } else {
      alert('Error adding to cart.');
    }
  })
  .catch(() => alert('Network error'));
}

function updateCartCount(count) {
  let cartCountElem = document.querySelector('.cart-count');
  if (!cartCountElem) {
    cartCountElem = document.createElement('span');
    cartCountElem.className = 'cart-count';
    document.querySelector('.cart').appendChild(cartCountElem);
  }
  cartCountElem.textContent = count;
}
</script>


</body>
</html>
