<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

// Get cart
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    die("No items in cart.");
}

// Compute total (in centavos)
$totalPrice = 0;
foreach ($cart as $product_id => $quantity) {
    $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
    if (!$result || mysqli_num_rows($result) === 0) continue;

    $row = mysqli_fetch_assoc($result);
    $totalPrice += $row['price'] * $quantity;
}
$amount = $totalPrice * 100; // PayMongo requires centavos

$paymentMethod = $_POST['payment_method'] ?? '';

if ($paymentMethod === "GCash") {
    // === PayMongo API Request ===
    $secretKey = "sk_test_4nZuhM53sis4p3Zh9YVzWTYc"; // sandbox Secret Key

    $ch = curl_init("https://api.paymongo.com/v1/sources");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode($secretKey . ":"),
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);

    $data = [
        "data" => [
            "attributes" => [
                "amount" => $amount,
                "redirect" => [
                    "success" => "https://vendo.wuaze.com/success.php",
                    "failed"  => "https://vendo.wuaze.com/failed.php"
                ],
                "type" => "gcash",
                "currency" => "PHP"
            ]
        ]
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);

    if ($response === false) {
        die("cURL Error: " . curl_error($ch));
    }

    curl_close($ch);
    $result = json_decode($response, true);

    if (isset($result['data']['id']) && isset($result['data']['attributes']['redirect']['checkout_url'])) {
        $paymongoId = $result['data']['id'];
        $checkoutUrl = $result['data']['attributes']['redirect']['checkout_url'];

        // === Save order in DB ===
        $stmt = $conn->prepare("INSERT INTO orders (payment_method, total_amount, status, paymongo_payment_id) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            die("MySQL Prepare failed: " . $conn->error);
        }

        $status = "pending";
        $stmt->bind_param("sdss", $paymentMethod, $totalPrice, $status, $paymongoId);

        if (!$stmt->execute()) {
            die("MySQL Execute failed: " . $stmt->error);
        }

        $stmt->close();

        // Clear cart after saving
        unset($_SESSION['cart']);

        // Redirect to PayMongo checkout
        header("Location: " . $checkoutUrl);
        exit;
    } else {
        echo "<pre>PayMongo Error: " . print_r($result, true) . "</pre>";
    }

} elseif ($paymentMethod === "Coins") {
    echo "Please insert coins...";
} else {
    echo "Invalid payment method.";
}
