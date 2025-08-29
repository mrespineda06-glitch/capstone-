<?php
include 'db.php';

$username = 'Dadulla';
$password = password_hash('123', PASSWORD_DEFAULT); // hashed password
$role = 'admin';

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $password, $role);

if ($stmt->execute()) {
    echo "Admin user inserted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
