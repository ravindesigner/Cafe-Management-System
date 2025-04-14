<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = preg_replace('/[^a-z0-9_-]/', '_', strtolower(trim($_POST['product_name'])));
    $quantity = intval($_POST['product_quantity']);
    $price = floatval($_POST['product_price']);

    $directory = "/var/www/coffee.siciit.com/images/";
    $pngPhoto = $directory . $name . ".png"; 
    $imageType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));

    if ($imageType !== 'png') {
        $message = "رێپێدراوە  png  تەنها فایلی";
    } elseif (move_uploaded_file($_FILES["product_image"]["tmp_name"], $pngPhoto)) {
        $stmt = $pdo->prepare("INSERT INTO storage (product_name, product_quantity, product_price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $quantity, $price]);
        $message = "بەرهەمی نوێ زیاد کرا";
    } else {
        $message = "شکست لە بارکردنی وێنە";
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
        <label>ناوی بەرهەم</label>
        <input type="text" name="product_name" required><br><br>

        <label>رێژەی بەرهەم</label>
        <input type="number" name="product_quantity" required><br><br>

        <label>نرخی بەرهەم</label>
        <input type="number" step="0.01" name="product_price" required><br><br>

        <label>وێنەی بەرهەم <span style="color:#ff7200l">(تەنها png)</label>
        <input type="file" name="product_image" accept="image/png" required><br><br>

        <button type="submit">زیادکردنی بەرهەم</button>
    </form>

    <?php if (isset($message)) echo "<p style='color: #ff7200;text-shadow: 0px 0px 10px #ff9b4b;'>$message</p>"; ?>

    <p><a href="admin_dashboard.php">← گەڕانەوە بۆ داشبۆڕد</a></p>
</div>
</body>
</html>

