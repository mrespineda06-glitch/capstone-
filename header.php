<?php


// Cart count for badge
$cart_count = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $qty) {
    $cart_count += $qty;
  }
}
?>

<!-- Head -->
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  * {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
    font-size: 12px;
  }
  body {
    margin: 0;
    padding-bottom: 70px;
    background-color: #f5f5f5;
  }
  .header {
    background-color: #ee4d2d;
    color: white;
    padding: 20px 30px;
    text-align: left;
    position: relative;
  }
  .header .message-icon {
    position: absolute;
    right: 15px;
    top: 1px;
    font-size: 40px;
    cursor: pointer;
    color: white;
  }
  .fas-fa-headset {
    font-size: 100px;
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
    height: 70px;
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

<!-- Header -->
<div class="header">
  <h1>VENDO</h1>
  <div class="message-icon" onclick="window.location.href='help_center.php';">
    <i class="fas fa-headset"></i>
  </div>
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

<!-- Cart JavaScript -->
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