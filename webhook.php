<?php
include 'db.php';

// Read raw payload
$payload = file_get_contents("php://input");
$event = json_decode($payload, true);

// Debug logging
file_put_contents("webhook_log.txt", date("Y-m-d H:i:s") . " " . $payload . PHP_EOL, FILE_APPEND);

if (!$event || !isset($event['data']['attributes']['type'])) {
    http_response_code(400);
    exit("Invalid payload");
}

$eventType = $event['data']['attributes']['type'];
$resourceId = $event['data']['id']; // could be pay_, pi_, or cs_
$attributes = $event['data']['attributes'];

switch ($eventType) {
    case "checkout_session.payment.paid":
    case "payment.paid":
        // Determine payment method
        $paymentMethod = $attributes['payment_method_used'] 
            ?? ($attributes['payment_method'] ?? "Unknown");

        // Try to find order by checkout_session_id first
        $checkoutId = $attributes['checkout_session_id'] ?? null;

        if ($checkoutId) {
            $stmt = $conn->prepare("
                UPDATE orders 
                SET status='paid', payment_method=?, paymongo_payment_id=? 
                WHERE checkout_session_id=? OR paymongo_payment_id=?
            ");
            $stmt->bind_param("ssss", $paymentMethod, $resourceId, $checkoutId, $resourceId);
        } else {
            // fallback: update by paymongo_payment_id only
            $stmt = $conn->prepare("
                UPDATE orders 
                SET status='paid', payment_method=? 
                WHERE paymongo_payment_id=?
            ");
            $stmt->bind_param("ss", $paymentMethod, $resourceId);
        }

        $stmt->execute();
        $stmt->close();
        break;

    case "payment.failed":
        $stmt = $conn->prepare("
            UPDATE orders 
            SET status='failed' 
            WHERE paymongo_payment_id=? OR checkout_session_id=?
        ");
        $stmt->bind_param("ss", $resourceId, $resourceId);
        $stmt->execute();
        $stmt->close();
        break;

    default:
        // Ignore other events
        break;
}

http_response_code(200);
echo "Webhook processed";
