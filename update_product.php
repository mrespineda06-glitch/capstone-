<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $product_name = $_POST['product_name'];
    $price = floatval($_POST['price']);

    // Handle image upload if new file is selected
    $imagePath = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $imagePath = basename($_FILES['product_image']['name']);
        move_uploaded_file($_FILES['product_image']['tmp_name'], "images/" . $imagePath);

        $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, product_image=? WHERE product_id=?");
        $stmt->bind_param("sdsi", $product_name, $price, $imagePath, $product_id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET product_name=?, price=? WHERE product_id=?");
        $stmt->bind_param("sdi", $product_name, $price, $product_id);
    }

    if ($stmt->execute()) {
        header("Location: admin.php?msg=updated");
        exit();
    } else {
        echo "Update failed.";
    }
}
?>
