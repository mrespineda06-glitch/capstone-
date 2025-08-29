<?php
include 'db.php';
session_start();

$username = $password = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: home.php");
        }
        exit;
    } else {
        $error = "❌ Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="login_register.css">
  <!-- Font Awesome for eye icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
  .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
    

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
  </style>
</head>
<body>
  <div class="form-box">
    <h1>Login</h1>
    <?php if (!empty($error)) echo "<p class='error' id='errorMessage'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($username) ?>">

      <div class="password-wrapper">
        <input type="password" name="password" placeholder="Password" id="password" required>
        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
      </div>

      <button type="submit">Login</button>
      <p>No account yet? <a href="register.php">Register</a></p>
    </form>
  </div>

<script>
// Toggle password visibility
const toggle = document.getElementById('togglePassword');
const password = document.getElementById('password');

toggle.addEventListener('click', function () {
  const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
  password.setAttribute('type', type);
  this.classList.toggle('fa-eye');
  this.classList.toggle('fa-eye-slash');
});

// Hide error message after 3 seconds
setTimeout(() => {
  const err = document.getElementById("errorMessage");
  if (err) err.style.display = "none";
}, 3000);
</script>

</body>
</html>
