<?php
include 'db.php';
session_start();
$user_id = 2; // Change based on logged-in user
$admin_id = 1; // Assumes admin has ID 1

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $msg = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_name, receiver_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $username, $admin_id, $msg);
    
    // Fetch user name
    $getUser = $conn->query("SELECT username FROM users WHERE user_id = $user_id");
    $row = $getUser->fetch_assoc();
    $username = $row['username'];
    
    $stmt->execute();
}
?>

<h2>Conversation with Admin</h2>
<form method="POST">
    <textarea name="message" required></textarea><br>
    <button type="submit">Send</button>
</form>

<?php
$result = $conn->query("SELECT * FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id ORDER BY created_at ASC");
while ($row = $result->fetch_assoc()) {
    echo "<p><strong>{$row['sender_name']}:</strong> {$row['message']} <em>({$row['created_at']})</em></p>";
}
?>
