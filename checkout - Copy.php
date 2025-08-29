<?php
session_start();
include 'db.php';

$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;

if (empty($cart)) {
  echo "<p>No items in cart.</p>";
  exit;
}

// 🔹 If form submitted, create PayMongo checkout session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = [];
    foreach ($cart as $product_id => $quantity) {
        $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
        if (!$result || mysqli_num_rows($result) === 0) continue;
        $row = mysqli_fetch_assoc($result);

        $items[] = [
            "currency" => "PHP",
            "amount" => intval($row['price'] * 100), // amount in cents
            "name" => $row['product_name'],
            "quantity" => intval($quantity)
        ];

        $totalPrice += $row['price'] * $quantity;
    }

    $data = [
        "data" => [
            "attributes" => [
                "line_items" => $items,
                "payment_method_types" => ["gcash","paymaya","card"], // PayMongo will show choices
                "success_url" => "https://vendo.wuaze.com/success.php",
                "cancel_url"  => "https://vendo.wuaze.com/failed.php"
            ]
        ]
    ];

    $ch = curl_init("https://api.paymongo.com/v1/checkout_sessions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode("sk_test_4nZuhM53sis4p3Zh9YVzWTYc") // put your secret key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    // 🔹 Save order in DB with "pending" status if checkout created
    if (isset($result['data']['id'])) {
       // $paymongo_payment_id = $result['data']['id']; // checkout_session.id
          $checkout_session_id = $result['data']['id']; // cs_...

        // Default to "GCash" for now (you can change this later)
        $payment_method = "GCash";

        /*$stmt = $conn->prepare("
            INSERT INTO orders (payment_method, total_amount, status, paymongo_payment_id) 
            VALUES (?, ?, 'pending', ?)
        ");
        $stmt->bind_param("sds", $payment_method, $totalPrice, $paymongo_payment_id);
        $stmt->execute();
        $stmt->close();
    }*/
        $stmt = $conn->prepare("
            INSERT INTO orders (payment_method, total_amount, status, checkout_session_id) 
            VALUES (?, ?, 'pending', ?)
        ");
        $stmt->bind_param("sds", $payment_method, $totalPrice, $checkout_session_id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($result['data']['attributes']['checkout_url'])) {
        header("Location: " . $result['data']['attributes']['checkout_url']);
        exit;
    } else {
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    .checkout-container {
      max-width: 1000px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      margin: 0px auto;
      background: #fff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .section-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 5px;
    }

    .product-item {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }

    .product-item img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 10px;
    }

    .product-info {
      flex-grow: 1;
    }

    .product-name {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .product-price {
      color: #ff4d00;
      font-weight: bold;
    }

    .total-section {
      text-align: right;
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
    }

    .checkout-button {
      display: block;
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      font-size: 16px;
      background-color: #ff4d00;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .checkout-button:hover {
      background-color: #e64400;
    }
  </style>
</head>
<body>

<div class="checkout-container">
  <div class="section-title">🛒 Selected Products</div>

  <?php foreach ($cart as $product_id => $quantity): 
    $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
    if (!$result || mysqli_num_rows($result) === 0) continue;

    $row = mysqli_fetch_assoc($result);
    $subtotal = $row['price'] * $quantity;
    $totalPrice += $subtotal;
  ?>
    <div class="product-item">
      <img src="images/<?= htmlspecialchars($row['product_image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
      <div class="product-info">
        <div class="product-name"><?= htmlspecialchars($row['product_name']) ?></div>
        <div>Qty: <?= $quantity ?> × ₱<?= number_format($row['price'], 2) ?></div>
        <div class="product-price">₱<?= number_format($subtotal, 2) ?></div>
      </div>
    </div>
  <?php endforeach; ?>

  <form method="POST">
    <div class="total-section">Total: ₱<?= number_format($totalPrice, 2) ?></div>
    <button type="submit" class="checkout-button">Proceed to Payment</button>
  </form>
</div>

</body>
</html>
