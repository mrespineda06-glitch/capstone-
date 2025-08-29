<?php
session_start();
include 'db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$profileImage = '';
$username = 'Login / Register';

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id"));

    $profileImage = $user['profile_image'];
    $username = $user['username'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }

        .profile-container {
            max-width: 600px;
            background: white;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-image-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .profile-image-wrapper img,
        .profile-image-wrapper .default-icon {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }

        .default-icon {
            background-color: #ddd;
            color: #888;
            font-size: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #007bff;
            color: white;
            padding: 12px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            border: 2px solid white;
            z-index: 10;
        }

        .edit-icon:hover {
            background-color: #0056b3;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 28px;
        }

        .login-register {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
        }

        .profile-links {
            margin-top: 30px;
            
        }

        .profile-links a {
            display: block;
            text-decoration: none;
            padding: 12px;
            color: #333;
            border-top: 1px solid #eee;
            font-size: 25px;
            transition: background 0.2s;
        }

        .profile-links a:hover {
            background: #f0f0f0;
        }

        .profile-links i {
            margin-right: 10px;
            color: #555;
        }

        #fileInput {
            display: none;
        }
    </style>
</head>
<body>
    
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image-wrapper">
                <?php if ($isLoggedIn && $profileImage): ?>
                    <img src="<?= $profileImage ?>" alt="Profile Image">
                <?php elseif ($isLoggedIn): ?>
                    <div class="default-icon"><i class="fas fa-user"></i></div>
                <?php else: ?>
                    <div class="default-icon"><i class="fas fa-user"></i></div>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <label class="edit-icon" for="fileInput"><i class="fas fa-pen"></i></label>
                    <form id="uploadForm" action="upload_profile.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_image" id="fileInput" onchange="document.getElementById('uploadForm').submit();">
                    </form>
                <?php endif; ?>
            </div>

            <?php if ($isLoggedIn): ?>
                <h2><?= htmlspecialchars($username) ?></h2>
            <?php else: ?>
                <a href="index.php" class="login-register">Login / Register</a>
            <?php endif; ?>
        </div>

        <?php if ($isLoggedIn): ?>
            <div class="profile-links">
                <a href="orders.php"><i class="fas fa-clock-rotate-left"></i> Purchase History</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
