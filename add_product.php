<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Basic field presence check
    if (
        empty($_POST['product_name']) ||
        !isset($_POST['price']) ||
        !isset($_POST['stock_quantity']) ||
        !isset($_FILES['product_image'])
    ) {
        header("Location: admin.php?msg=missing");
        exit();
    }

    $product_name = trim($_POST['product_name']);
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];

    // Validate name length
    if (strlen($product_name) < 2) {
        header("Location: admin.php?msg=invalid");
        exit();
    }

    // Validate price
    if (!is_numeric($price) || floatval($price) <= 0) {
        header("Location: admin.php?msg=invalidprice");
        exit();
    }

    // Validate stock quantity
    if (!is_numeric($stock_quantity) || intval($stock_quantity) < 0) {
        header("Location: admin.php?msg=invalid");
        exit();
    }

    $price = floatval($price);
    $stock_quantity = intval($stock_quantity);

    // Image handling
    if ($_FILES['product_image']['error'] === 0) {
        $upload_dir = 'images/';
        $originalName = $_FILES['product_image']['name'];
        $imageExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExt, $allowedExts)) {
            header("Location: admin.php?msg=invalid");
            exit();
        }

        $imageName = uniqid("img_") . "." . $imageExt;
        $target_path = $upload_dir . $imageName;

        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path)) {
            header("Location: admin.php?msg=uploadfail");
            exit();
        }
    } else {
        header("Location: admin.php?msg=uploadfail");
        exit();
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO products (product_name, price, stock_quantity, product_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $product_name, $price, $stock_quantity, $imageName);

    if ($stmt->execute()) {
        header("Location: admin.php?msg=added");
        exit();
    } else {
        header("Location: admin.php?msg=dberror");
        exit();
    }
}
?>
