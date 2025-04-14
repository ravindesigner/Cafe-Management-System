<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('/var/www/coffee.siciit.com/config.php');
?>
<?php
$lowStockStmt = $pdo->query("SELECT product_name, product_quantity FROM storage WHERE product_quantity <= 5");
$lowStockItems = $lowStockStmt->fetchAll();
?>

<?php if ($lowStockItems): ?>
  <div style="
    padding: 
    5px;
	font-size:18px;
    color: #fd3f14;
    text-shadow:0px 0px 12px red;
    margin-bottom: 30px;
    ">
        <strong> ئاگاداری کەمبوونی کاڵا</strong>
        <ul>
                            <li>hot_chocolate — تەنها 2 دانە ماوە!</li>
                    </ul>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>داشبۆردی ئەدمین</title>
    <link rel="stylesheet" href="style.css">
	<style>body{display:block;}</style>
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
            <li><a href="admin_logout.php"><span style="color: #ff7200;text-shadow: 0px 0px 10px #ff9b4b;">چوونەدەرەوە</a></li>
        </ul>
    </div>
</body>
</html>
