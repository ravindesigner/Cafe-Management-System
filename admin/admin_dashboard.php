<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('/var/www/coffee.siciit.com/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>داشبۆردی ئەدمین</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard">
        <h1>داشبۆردی ئەدمین</h1>
        <h2>! <?php echo htmlspecialchars($_SESSION['username']); ?> بەخێربێیت </h2>
<br>
        <ul>
            <li><a href="create_user.php">دروستکردنی بەکارهێنەری نوێ</a></li>
			<br>
            <li><a href="modify_user.php">گۆڕانکاری بەکارهێنەرەکان</a></li><br>
<li><a href="add_product.php">گۆڕانکاری بەرهەمەکان</a></li><br>
<li><a href="add_new.php">زیاد کردنی بەرهەمی نوێ</a></li><br>
            <li><a href="view_sales.php">بینینی فرۆشتنەکان</a></li><br>
            <li><a href="sales_chart.php">خشتەی فرۆشتنەکان</a></li><br>
            <li><a href="admin_logout.php">چوونەدەرەوە</a></li>
        </ul>
    </div>
</body>
</html>
