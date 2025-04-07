<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>داشبۆردی بەکارهێنەر</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-box">
        <h1>! <?= htmlspecialchars($_SESSION['username']) ?> بەخێربێیت </h1>
        <h2>ئەمە داشبۆردی کاشێرە ، دەتوانیت داواکاری کافێ ئامادە بکەیت.</h2><br>
        <a href="order.php" class="btn">داواکاری نوێ</a><br><br>
        <a href="logout.php" class="btn logout">چوونەدەرەوە</a>
    </div>
</body>
</html>

