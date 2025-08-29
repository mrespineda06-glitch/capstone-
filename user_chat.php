<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    die("Access denied. Users only.");
}

$user_id = $_SESSION['user_id'];

// Get admin ID (assume always user_id = 1)
$admin_id = 1;

// Handle message from user to admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $msg = $_POST['message'];
    $user_name = $_SESSION['username'] ?? 'User';

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_name, receiver_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $user_name, $admin_id, $msg);
    $stmt->execute();
}
?>

<h2>Conversation with Admin</h2>
<?php
$chat = $conn->query("SELECT * FROM messages WHERE 
    (sender_id = $user_id AND receiver_id = $admin_id) OR 
    (sender_id = $admin_id AND receiver_id = $user_id) 
    ORDER BY created_at ASC");

while ($msg = $chat->fetch_assoc()) {
    $sender = htmlspecialchars($msg['sender_name'] ?? '');
    $message = htmlspecialchars($msg['message'] ?? '');
    $time = $msg['created_at'];
    echo "<p><strong>$sender:</strong> $message <em>($time)</em></p>";
}
?>

<form method="POST">
    <textarea name="message" required></textarea><br>
    <button type="submit">Send Message</button>
</form>
