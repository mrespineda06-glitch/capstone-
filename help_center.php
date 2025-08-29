<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$admin_id = 6;

// Fetch conversation
$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $admin_id, $admin_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Help Center - VendiPay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
    body { background-color: #f9f9f9; }

    header {
      background-color: #ee4d2d;
      padding: 15px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header nav a {
      color: white;
      text-decoration: none;
      margin: 0 12px;
      font-size: 15px;
    }

    .container {
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .icon-title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
    }

    .icon-title i {
      font-size: 22px;
      color: #ee4d2d;
    }

    .instructions ul {
      list-style: none;
      padding-left: 0;
    }

    .instructions li {
      margin-bottom: 10px;
      font-size: 15px;
    }

    .instructions li i {
      color: #ee4d2d;
      margin-right: 8px;
    }

    /* Floating Chat Icon */
    .chat-toggle {
      position: fixed;
      bottom: 25px;
      right: 25px;
      background-color: #ee4d2d;
      color: white;
      width: 55px;
      height: 55px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      cursor: pointer;
      z-index: 1000;
    }

    .chat-toggle:hover {
      background-color: #d9431d;
    }

    /* Chat Popup Container */
    #chat-container {
      display: none;
      position: fixed;
      bottom: 95px;
      right: 25px;
      width: 300px;
      max-height: 550px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
      z-index: 999;
      padding: 15px;
    }

    .chat-box {
      max-height: 300px;
      overflow-y: auto;
      border: 1px solid #ddd;
      padding: 10px;
      background: #f5f5f5;
      border-radius: 8px;
      margin-bottom: 10px;
    }

    .message-wrapper {
      display: flex;
      margin-bottom: 10px;
    }

    .sent {
      justify-content: flex-end;
    }

    .received {
      justify-content: flex-start;
    }

    .message {
      padding: 10px 14px;
      border-radius: 12px;
      max-width: 70%;
      font-size: 12px;
      word-wrap: break-word;
    }

    .sent .message {
      background-color: #ee4d2d;
      color: white;
      border-bottom-right-radius: 0;
    }

    .received .message {
      background-color: #e0e0e0;
      color: #333;
      border-bottom-left-radius: 0;
    }

    .message img {
      margin-top: 8px;
      max-width: 100%;
      border-radius: 6px;
    }

    form textarea {
      width: 100%;
      padding: px;
      resize: none;
      height: 35px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 12px;
      margin-bottom: 5px;
    }

    form input[type="file"] {
      margin-bottom: 8px;
      font-size: 12px;
    }

    form button {
      padding: 8px 14px;
      background-color: #ee4d2d;
      border: none;
      color: white;
      font-size: 12px;
      border-radius: 5px;
      cursor: pointer;
    }

    form button:hover {
      background-color: #d9431d;
    }

    #status {
      font-size: 13px;
      color: green;
      margin-bottom: 6px;
    }
  </style>
</head>
<body>

<header>
  <nav>
    <a href="home.php"><i class="fas fa-home"></i> Home</a>
    <a href="help_center.php"><i class="fas fa-headset"></i> Support</a>
  </nav>
</header>

<div class="container">
  <div class="icon-title">
    <i class="fas fa-info-circle"></i>
    <h2>Help & Instructions</h2>
  </div>

  <div class="instructions">
    <ul>
      <li><i class="fas fa-shopping-cart"></i> Add items to your cart.</li>
      <li><i class="fas fa-credit-card"></i> Pay via GCash or Coins.</li>
      <li><i class="fas fa-box-open"></i> Your item is automatically dispensed.</li>
      <li><i class="fas fa-user"></i> Check your profile for purchase history.</li>
      <li><i class="fas fa-headset"></i> Tap the chat icon to contact support.</li>
    </ul>
  </div>
</div>

<!-- Chat Toggle Button -->
<div class="chat-toggle" onclick="toggleChat()">
  <i class="fas fa-comments"></i>
</div>

<!-- Chat Container -->
<div id="chat-container">
  <h3 style="margin-bottom: 10px;">Chat with Support</h3>
  <div class="chat-box" id="chat-box">
    <?php if (count($messages) > 0): ?>
      <?php foreach ($messages as $msg): ?>
        <div class="message-wrapper <?php echo $msg['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
          <div class="message">
            <?= nl2br(htmlspecialchars($msg['message'])) ?>
            <?php if (!empty($msg['file']) && file_exists($msg['file'])): ?>
              <br><img src="<?= htmlspecialchars($msg['file']) ?>" alt="Attachment">
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center; color:#888;">No messages yet.</p>
    <?php endif; ?>
  </div>

  <div id="status"></div>
  <form id="messageForm" enctype="multipart/form-data">
    <input type="hidden" name="sender_id" value="<?= $user_id ?>">
    <input type="hidden" name="receiver_id" value="<?= $admin_id ?>">
    <textarea name="message" placeholder=" Type your message..."></textarea>
    <input type="file" name="file" accept="image/*">
    <button type="submit"><i class="fas fa-paper-plane"></i> Send</button>
  </form>
</div>

<script>
function toggleChat() {
  const chatBox = document.getElementById('chat-container');
  chatBox.style.display = chatBox.style.display === 'block' ? 'none' : 'block';
}

document.getElementById("messageForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const statusDiv = document.getElementById("status");

  fetch("send_message.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    if (response.trim() === "success") {
      statusDiv.textContent = "Message sent!";
      form.reset();

      fetch("help_center.php")
        .then(res => res.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, "text/html");
          const newChat = doc.getElementById("chat-box").innerHTML;
          document.getElementById("chat-box").innerHTML = newChat;
          document.getElementById("chat-box").scrollTop = document.getElementById("chat-box").scrollHeight;
        });
    } else {
      statusDiv.textContent = "Failed to send.";
      statusDiv.style.color = "red";
    }
  })
  .catch(() => {
    statusDiv.textContent = "Error occurred.";
    statusDiv.style.color = "red";
  });
});
</script>

</body>
</html>
