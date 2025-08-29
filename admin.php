<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - Vending Machine</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f7f7f7;
    }

    header {
      background-color: #ee4d2d;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .form-section, .product-grid {
      max-width: 900px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .form-section h2, .product-grid h2 {
      margin-bottom: 50px;
    }

    form input, form button {
      display: block;
      margin-bottom: 15px;
      padding: 10px;
      width: 100%;
    }

    .grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .product-card {
      background: #fefefe;
      padding: 15px;
      border-radius: 10px;
      width: 200px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .product-card img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
    }

    .product-card h4 {
      margin: 10px 0 5px;
    }

    .product-card p {
      margin: 0 0 10px;
    }

    .product-card form input, .product-card form button {
      width: 100%;
      padding: 8px;
    }

    button {
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }

    button:hover {
      background-color: #218838;
    }

    .delete-button {
      background-color: #dc3545;
    }

    .edit-button {
      background-color: #007bff;
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
      height: 120px;
      z-index: 1000;
    }

    .nav-item {
      text-align: center;
      font-size: 25px;
      color: #444;
      text-decoration: none;
    }

    .nav-item i {
      font-size: 50px;
      display: block;
      margin-bottom: 2px;
    }

    /* Message styling */
    .message {
      padding: 10px 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      font-weight: bold;
      width: fit-content;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
  .message {
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 14px;
  }

  .message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  .message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }


  </style>
</head>
<body>

  <section class="form-section">
    <h2>Add New Product</h2>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['msg'])): ?>
      <?php
        $msgType = $_GET['msg'];
        $messages = [
          'added' => ['✅ Product added successfully.', 'success'],
          'invalid' => ['❌ Invalid input detected.', 'error'],
          'invalidprice' => ['❌ Price cannot be 0.', 'error'],
          'uploadfail' => ['❌ Image upload failed.', 'error'],
          'missing' => ['❌ Missing required fields.', 'error'],
          'dberror' => ['❌ Failed to add product to database.', 'error'],
        ];

        if (isset($messages[$msgType])) {
          [$text, $type] = $messages[$msgType];
          echo "<div class='message $type'>$text</div>";
        }
      ?>
    <?php endif; ?>
     <div id="messageContainer"></div>

    <form id="addForm" action="add_product.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
      <input type="text" name="product_name" placeholder="Product Name" required>
      <input type="number" name="price" id="price" placeholder="Price (₱)" step="0.01" required>
      <input type="number" name="stock_quantity" id="stock_quantity" placeholder="Stock Quantity" required>
      <input type="file" name="product_image" id="product_image" accept="image/*" required>
      <button type="submit">Add Product</button>
    </form>
  </section>

  <section class="product-grid">
    <h2>Product Inventory</h2> 
    <div class="grid">
      <?php
        $query = "SELECT * FROM products";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
      ?>
      <div class="product-card" style="<?php echo $row['stock_quantity'] == 0 ? 'opacity: 0.3;' : ''; ?>">

        <img src="images/<?php echo htmlspecialchars($row['product_image'] ?: 'default.png'); ?>" alt="Product Image">
        <h4><?php echo htmlspecialchars($row['product_name']); ?></h4>
        <p>Price: ₱<?php echo number_format($row['price'], 2); ?></p>
       <p>
  Stock:
  <?php if ($row['stock_quantity'] == 0): ?>
    <span style="color: red; font-weight: bold;">Out of Stock</span>
  <?php else: ?>
    <?php echo $row['stock_quantity']; ?>
  <?php endif; ?>
</p>


        <!-- Update Stock -->
        <form action="update_stock.php" method="POST">
          <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
          <input type="number" name="new_quantity" placeholder="New Stock" min="0" required>
          <button type="submit">Update Stock</button>
        </form>

        <!-- Edit Product -->
        <form action="edit_product.php" method="GET">
          <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
          <button type="submit" class="edit-button">Edit</button>
        </form>

        <!-- Delete Product -->
        <form action="delete_product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
          <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
          <button type="submit" class="delete-button">Delete</button>
        </form>
      </div>
      <?php endwhile; else: ?>
        <p>No products found.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Bottom Navigation -->
  <div class="bottom-nav">
    <a href="admin.php" class="nav-item">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="reachout.php" class="nav-item">
      <i class="fas fa-headset"></i>
      <span>Messages</span>
    </a>
    <a href="me.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Me</span>
    </a>
  </div>

  <script>
    
  function showMessage(message, type = 'error') {
    const container = document.getElementById('messageContainer');
    if (container) {
      container.innerHTML = `<div class="message ${type}">${message}</div>`;
      setTimeout(() => {
        container.innerHTML = '';
      }, 3000);
    }
  }

  function validateForm() {
    const price = parseFloat(document.getElementById('price').value);
    const stock = parseInt(document.getElementById('stock_quantity').value);
    const image = document.getElementById('product_image').files[0];

    if (isNaN(price) || price <= 0) {
      showMessage("❌ Price must be greater than 0.");
      return false;
    }

    if (isNaN(stock) || stock < 0) {
      showMessage("❌ Stock must be a non-negative number.");
      return false;
    }

    if (image) {
      const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
      if (!allowedTypes.includes(image.type)) {
        showMessage("❌ Only JPG, JPEG, or PNG images are allowed.");
        return false;
      }

      const maxSize = 2 * 1024 * 1024;
      if (image.size > maxSize) {
        showMessage("❌ Image size must be less than 2MB.");
        return false;
      }
    }

    return true;
  }

  // Hide backend messages after 3s (already handled in admin.php)
  setTimeout(() => {
    const msg = document.querySelector('.message');
    if (msg) msg.style.display = 'none';
  }, 3000);
</script>

  

</body>
</html>