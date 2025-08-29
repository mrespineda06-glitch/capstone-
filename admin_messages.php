<?php
session_start();
include 'db.php';

// Get all users with role 'user'
$users = $conn->query("SELECT * FROM users WHERE role = 'user'");

// If user is selected
$selected_user = null;
$messages = [];

if (isset($_GET['user_id'])) {
    $selected_user = $_GET['user_id'];

    // Get messages between admin (0) and selected user
    $stmt = $conn->prepare("SELECT * FROM messages 
                            WHERE (sender_id = 0 AND receiver_id = ?) 
                               OR (sender_id = ? AND receiver_id = 0)
                            ORDER BY created_at ASC");
    $stmt->bind_param("ii", $selected_user, $selected_user);
    $stmt->execute();
    $messages = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Messages</title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #f5f5f5;
            border-right: 1px solid #ccc;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar ul {
            padding: 0;
            list-style: none;
        }
        .sidebar li {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .sidebar a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
        }
        .sidebar img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .chat {
            flex: 1;
            padding: 20px;
            background: #fff;
            display: flex;
            flex-direction: column;
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .message {
            max-width: 70%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
        }
        .from-admin {
            background: #dcf8c6;
            align-self: flex-end;
        }
        .from-user {
            background: #f1f1f1;
            align-self: flex-start;
        }
        form textarea {
            width: 100%;
            height: 60px;
            padding: 10px;
            font-size: 14px;
            resize: none;
        }
        button {
            padding: 10px 20px;
            background: #4caf50;
            color: white;
            border: none;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <ul>
        <?php if ($users->num_rows > 0): ?>
            <?php while ($row = $users->fetch_assoc()): ?>
                <li>
                    <a href="?user_id=<?= $row['user_id'] ?>">
                        <img src="<?= htmlspecialchars($row['profile_image']) ?>" alt="Profile">
                        <?= htmlspecialchars($row['username']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No users found.</li>
        <?php endif; ?>
    </ul>
</div>

<div class="chat">
    <div class="messages">
        <?php if ($selected_user && $messages->num_rows > 0): ?>
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div class="message <?= $msg['sender_id'] == 0 ? 'from-admin' : 'from-user' ?>">
                    <?= htmlspecialchars($msg['message']) ?><br>
                    <small><?= $msg['created_at'] ?></small>
                </div>
            <?php endwhile; ?>
        <?php elseif ($selected_user): ?>
            <p>No messages yet.</p>
        <?php else: ?>
            <p>Select a user to start chatting.</p>
        <?php endif; ?>
    </div>

    <?php if ($selected_user): ?>
        <form action="send_message.php" method="POST">
            <input type="hidden" name="receiver_id" value="<?= $selected_user ?>">
            <textarea name="message" placeholder="Type your message..."></textarea>
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
