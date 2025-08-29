<?php
include 'db.php';

if (!isset($_GET['product_id'])) {
    echo "No product selected.";
    exit();
}

$product_id = intval($_GET['product_id']);
$query = "SELECT * FROM products WHERE product_id = $product_id";
$result = $conn->query($query);

if (!$result || $result->num_rows == 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <!-- Bootstrap CDN for styling -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Edit Product</h2>

  <form action="update_product.php" method="POST" enctype="multipart/form-data" class="border p-4 bg-white rounded shadow-sm">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

    <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" id="product_name" name="product_name" class="form-control" 
             value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
    </div>

    <div class="mb-3">
      <label for="price" class="form-label">Price ($)</label>
      <input type="number" id="price" name="price" step="0.01" class="form-control"
             value="<?php echo htmlspecialchars($product['price']); ?>" required>
    </div>

    <div class="mb-3">
      <label for="product_image" class="form-label">Product Image</label>
      <input type="file" id="product_image" name="product_image" accept="image/*" class="form-control">
    </div>

    <?php if (!empty($product['image_path'])): ?>
      <div class="mb-3">
        <label class="form-label">Current Image:</label><br>
        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" style="max-width: 200px;">
      </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Update Product</button>
    <a href="product_list.php" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>

</body>
</html>
