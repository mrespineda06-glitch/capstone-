<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$admin_id = 6;

$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $admin_id, $admin_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

foreach ($messages as $msg): ?>
  <div class="message-wrapper <?= $msg['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
    <div class="message">
      <?= nl2br(htmlspecialchars($msg['message'])) ?>
      <?php if (!empty($msg['file']) && file_exists($msg['file'])): ?>
        <br><img src="<?= htmlspecialchars($msg['file']) ?>" alt="Attachment">
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
