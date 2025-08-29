<?php
include 'db.php';
session_start();

$error = '';
$showError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($username) < 4) {
        $error = "Username must be at least 4 characters.";
        $showError = true;
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $error = "Username already taken.";
            $showError = true;
        } else {
            if (strlen($password) < 6) {
                $error = "Password must be at least 6 characters.";
                $showError = true;
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
                $stmt->bind_param("ss", $username, $hashed);
                if ($stmt->execute()) {
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed.";
                    $showError = true;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link rel="stylesheet" href="login_register.css">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h1>Register</h1>

    <?php if ($showError): ?>
      <p class="error" id="errorMessage"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>

      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="Password" required>
        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
      </div>

      <button type="submit">Register</button>
      <p>Already have an account? <a href="index.php">Login</a></p>
    </form>
  </div>

<script>
const toggle = document.getElementById('togglePassword');
const password = document.getElementById('password');

toggle.addEventListener('click', function () {
  const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
  password.setAttribute('type', type);
  this.classList.toggle('fa-eye');
  this.classList.toggle('fa-eye-slash');
});

// Hide error after 3 seconds
setTimeout(() => {
  const err = document.getElementById("errorMessage");
  if (err) err.style.display = "none";
}, 3000);
</script>
</body>
</html>
