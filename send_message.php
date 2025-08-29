<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = intval($_POST['sender_id']);
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']) ?? '';
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // create if doesn't exist
        }

        $tmp_name = $_FILES['file']['tmp_name'];
        $original_name = basename($_FILES['file']['name']);
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_name = uniqid('img_', true) . '.' . $ext;
        $target_file = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $file_path = $target_file;
        }
    }

    // Insert message into DB
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, file, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $file_path);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
