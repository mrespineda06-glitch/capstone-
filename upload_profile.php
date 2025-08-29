<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in.";
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image'])) {
    $img = $_FILES['profile_image'];
    $imgName = basename($img['name']);
    $targetDir = "images/";
    $targetFile = $targetDir . time() . "_" . $imgName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Allow only image types
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    if (move_uploaded_file($img["tmp_name"], $targetFile)) {
        // Save new image path to DB
        $query = "UPDATE users SET profile_image = '$targetFile' WHERE user_id = $user_id";
        mysqli_query($conn, $query);

        // Redirect back to profile
        header("Location: me.php");
        exit;
    } else {
        echo "Failed to upload image.";
    }
} else {
    echo "No file uploaded.";
}
?>
