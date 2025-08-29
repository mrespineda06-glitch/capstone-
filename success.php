<?php
// success.php

// Your PayMongo Secret Key (sandbox key for testing)
$secretKey = "sk_test_4nZuhM53sis4p3Zh9YVzWTYc"; // replace with your real sandbox secret key

// Get payment/checkout session ID from PayMongo redirect
$paymentId = isset($_GET['id']) ? $_GET['id'] : null;

$paymentData = null;

if ($paymentId) {
    // Initialize cURL
    $ch = curl_init("https://api.paymongo.com/v1/payments/$paymentId");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode($secretKey . ":"),
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $paymentData = json_decode($response, true);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Success</title>
  <style>
    body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
    .card {
      display: inline-block; padding: 20px;
      border: 1px solid #ccc; border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h1 { color: green; }
    table { margin: 20px auto; border-collapse: collapse; }
    td, th { padding: 8px 12px; border: 1px solid #ccc; }
  </style>
</head>
<body>
  <div class="card">
    <h1>✅ Payment Successful!</h1>
    <p>Thank you for your payment.</p>

    <?php if ($paymentData && isset($paymentData['data']['attributes'])): 
        $attrs = $paymentData['data']['attributes']; ?>
        <table>
          <tr><th>Reference ID</th><td><?php echo htmlspecialchars($transaction_id); ?></td></tr>
          <tr><th>Amount</th><td>₱<?php echo number_format($attrs['total_amount'] / 100, 2); ?></td></tr>
          <tr><th>Status</th><td><?php echo htmlspecialchars($attrs['payment_status']); ?></td></tr>
          <tr><th>Payment Method</th><td><?php echo htmlspecialchars($attrs['payment_method']); ?></td></tr>
          <tr><th>Date</th><td><?php echo date("Y-m-d H:i:s", strtotime($attrs['order_time'])); ?></td></tr>
        </table>
    <?php else: ?>
        <p><em>Could not retrieve payment details.</em></p>
    <?php endif; ?>
  </div>
</body>
</html>
