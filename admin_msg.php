<?php
include 'db.php';
$admin_id = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to_user = $_POST['receiver_id'];
    $msg = $_POST['message'];

    $getAdmin = $conn->query("SELECT username FROM users WHERE user_id = $admin_id");
    $row = $getAdmin->fetch_assoc();
    $admin_name = $row['username'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_name, receiver_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $admin_id, $admin_name, $to_user, $msg);
    $stmt->execute();
}
?>

<h2>Inbox</h2>
<?php
$users = $conn->query("SELECT DISTINCT sender_id, sender_name FROM messages WHERE receiver_id = $admin_id");
while ($u = $users->fetch_assoc()) {
    $uid = $u['sender_id'];
    echo "<h3>Conversation with {$u['sender_name']}</h3>";

    $chat = $conn->query("SELECT * FROM messages WHERE (sender_id = $uid AND receiver_id = $admin_id) OR (sender_id = $admin_id AND receiver_id = $uid) ORDER BY created_at ASC");
    while ($msg = $chat->fetch_assoc()) {
        echo "<p><strong>{$msg['sender_name']}:</strong> {$msg['message']} <em>({$msg['created_at']})</em></p>";
    }
?>
    <form method="POST">
        <input type="hidden" name="receiver_id" value="<?= $uid ?>">
        <textarea name="message" required></textarea><br>
        <button type="submit">Reply</button>
    </form>
<?php
}
?>
