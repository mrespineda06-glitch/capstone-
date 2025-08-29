<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Handle message + optional file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_user_id) {
    $message = trim($_POST['message']);
    $filename = null;

    if (!empty($_FILES['file']['name'])) {
        $targetDir = "images/";
        $filename = uniqid() . "_" . basename($_FILES["file"]["name"]);
        $targetFile = $targetDir . $filename;
        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
        $filename = $targetFile; // store full path like images/xyz.jpg
    }

    if (!empty($message) || $filename) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_name, receiver_id, message, file) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiss", $admin_id, $_SESSION['username'], $selected_user_id, $message, $filename);
        $stmt->execute();
    }

    header("Location: reachout.php?user_id=" . $selected_user_id);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Your existing CSS remains untouched */
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 0; }
        .chat-container { display: flex; height: 100vh; }
        .user-list { width: 280px; border-right: 1px solid #ccc; overflow-y: auto; background: #fff; }
        .user-entry { padding: 15px; border-bottom: 1px solid #eee; display: flex; gap: 10px; cursor: pointer; }
        .user-entry:hover { background-color: #f5f5f5; }
        .avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #ccc; }
        .user-details { flex: 1; }
        .user-details .name { font-weight: bold; margin-bottom: 5px; }
        .chat-box { flex: 1; display: flex; flex-direction: column; background: #f9f9f9; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; }
        .chat-bubble { display: inline-block; max-width: 70%; padding: 10px 14px; border-radius: 18px; margin-bottom: 12px; font-size: 14px; line-height: 1.4; word-wrap: break-word; position: relative; }
        .admin { align-self: flex-end; background-color: #d1e7ff; border-bottom-right-radius: 0; text-align: right; }
        .user { align-self: flex-start; background-color: #e4e4e4; border-bottom-left-radius: 0; }
        .timestamp { font-size: 11px; color: gray; margin-top: 4px; }
        .chat-image { max-width: 100%; border-radius: 10px; margin-top: 5px; }
        .chat-form { padding: 15px; border-top: 1px solid #ccc; background: white; }
        .input-wrapper { display: flex; align-items: center; gap: 10px; }
        .input-wrapper textarea { flex: 1; height: 50px; resize: none; padding: 8px 12px; font-size: 14px; }
        .input-wrapper .plus-icon { display: inline-flex; justify-content: center; align-items: center; font-size: 24px; width: 36px; height: 36px; background-color: #007bff; color: white; border-radius: 50%; cursor: pointer; transition: background 0.2s; }
        .input-wrapper .plus-icon:hover { background-color: #0056b3; }
        .input-wrapper input[type="file"] { display: none; }
        .input-wrapper button { padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; }
        @media (max-width: 768px) {
            .chat-container { flex-direction: column; }
            .user-list { width: 100%; max-height: 200px; }
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="user-list">
        <?php
        $users = $conn->query("SELECT u.user_id, u.username, 
            (SELECT message FROM messages WHERE (sender_id = u.user_id AND receiver_id = $admin_id) 
             OR (sender_id = $admin_id AND receiver_id = u.user_id) ORDER BY created_at DESC LIMIT 1) AS last_msg,
            (SELECT created_at FROM messages WHERE (sender_id = u.user_id AND receiver_id = $admin_id) 
             OR (sender_id = $admin_id AND receiver_id = u.user_id) ORDER BY created_at DESC LIMIT 1) AS last_time
            FROM users u
            WHERE u.role != 'admin'
            ORDER BY last_time DESC
        ");
        while ($user = $users->fetch_assoc()) {
            $preview = htmlspecialchars($user['last_msg'] ?? '');
            $date = $user['last_time'] ? date('M d', strtotime($user['last_time'])) : '';
            $profileImage = 'images/profile-placeholder.png';
            $userProfile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT profile_image FROM users WHERE user_id = {$user['user_id']}"));
            if (!empty($userProfile['profile_image']) && file_exists($userProfile['profile_image'])) {
                $profileImage = $userProfile['profile_image'];
            }

            echo "
            <a href='?user_id={$user['user_id']}' style='text-decoration: none; color: inherit;'>
              <div class='user-entry'>
                <img src='{$profileImage}' class='avatar' alt='Avatar'>
                <div class='user-details'>
                  <div class='name'>{$user['username']}</div>
                  <div style='font-size: 12px; color: gray;'>{$preview}</div>
                </div>
                <div style='font-size: 12px; color: gray;'>$date</div>
              </div>
            </a>";
        }
        ?>
    </div>

    <!-- Chat Section -->
    <div class="chat-box">
        <div class="chat-messages">
            <?php
            if ($selected_user_id) {
                $stmt = $conn->prepare("SELECT * FROM messages WHERE 
                    (sender_id = ? AND receiver_id = ?) OR 
                    (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
                $stmt->bind_param("iiii", $admin_id, $selected_user_id, $selected_user_id, $admin_id);
                $stmt->execute();
                $messages = $stmt->get_result();

                while ($msg = $messages->fetch_assoc()) {
                    $isAdmin = $msg['sender_id'] == $admin_id;
                    $text = htmlspecialchars($msg['message']);
                    $time = date('M d, h:i A', strtotime($msg['created_at']));
                    $bubbleClass = $isAdmin ? 'admin' : 'user';

                    echo "<div class='chat-bubble {$bubbleClass}'>";
                    if ($text) echo $text;

                    if (!empty($msg['file'])) {
                        $filePath = $msg['file'];
                        if (!str_starts_with($filePath, 'images/')) {
                            $filePath = 'images/' . $filePath;
                        }
                        if (file_exists($filePath)) {
                            echo "<br><img src='{$filePath}' class='chat-image' alt='File'>";
                        }
                    }

                    echo "<div class='timestamp'>{$time}</div></div>";
                }
            } else {
                echo "<p style='padding: 20px;
                </p>";
            }
            ?>
        </div>

        <?php if ($selected_user_id): ?>
        <div class="chat-form">
            <form method="POST" enctype="multipart/form-data">
                <div class="input-wrapper">
                    <textarea name="message" placeholder="Type a message..."></textarea>
                    <label for="file-upload" class="plus-icon">+</label>
                    <input type="file" id="file-upload" name="file" accept="image/*">
                    <button type="submit">Send</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
