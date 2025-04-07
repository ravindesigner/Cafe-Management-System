<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $quantity = intval($_POST['product_quantity']);
    $price = floatval($_POST['product_price']);

    $target_dir = "/var/www/coffee.siciit.com/images/";
    $filename = basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $filename;
    $imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($imageType !== 'png') {
        $message = "تەنها فایلی png ڕێپێدراوە";
    } elseif (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        
        $stmt = $pdo->prepare("INSERT INTO storage (product_name, product_quantity, product_price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $quantity, $price]);
        $message = "بەرهەمی نوێ زیاد کرا";
    } else {
        $message = "شکست لە بارکردنی وێنە.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>زیادکردنی بەرهەمی نوێ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard">
    <h2>زیادکردنی بەرهەمی نوێ</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>ناوی بەرهەم:</label>
        <input type="text" name="product_name" required><br><br>

        <label>رێژەی بەرهەم:</label>
        <input type="number" name="product_quantity" required><br><br>

        <label>نرخی بەرهەم:</label>
        <input type="number" step="0.01" name="product_price" required><br><br>

        <label>وێنەی بەرهەم (تەنها png):</label>
        <input type="file" name="product_image" accept="image/png" required><br><br>

        <button type="submit">زیادکردنی بەرهەم</button>
    </form>

    <?php if (isset($message)) echo "<p style='color: lime;'>$message</p>"; ?>

    <p><a href="admin_dashboard.php">← گەڕانەوە بۆ داشبۆڕد</a></p>
</div>
</body>
</html>

