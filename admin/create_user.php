<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('/var/www/coffee.siciit.com/config.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $email      = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $is_admin   = isset($_POST['is_admin']) ? 1 : 0;

  
    $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
        $message = "ناوی بەکارهێنەر دوبارەیە!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, first_name, last_name, is_admin) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $email, $first_name, $last_name, $is_admin]);

        $message = "بەکارهێنەر دروستکرا";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>دروستکردنی بەکارهێنەر</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard">
    <h2>زیادکردنی بەکارهێنەری نوێ</h2>

    <form action="create_user.php" method="POST">
        <label>ناوی بەکارهێنەر:</label>
        <input type="text" name="username" required><br>

        <label>ووشەی نهێنی:</label>
        <input type="password" name="password" required><br>

        <label>پۆستی ئەلکترۆنی:</label>
        <input type="email" name="email" required><br>

        <label>ناوی یەکەم:</label>
        <input type="text" name="first_name" required><br>

        <label>ناوی کۆتا:</label>
        <input type="text" name="last_name" required><br>

        <label><input type="checkbox" name="is_admin"> پێدانی ئەدمینی</label><br>

        <button type="submit">زیاکردنی بەکارهێنەر</button>
    </form>

    <?php if ($message): ?>
        <p style="color: <?= strpos($message, 'سەرکەوتوو') !== false ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <p><a href="admin_dashboard.php">گەڕانەوە بۆ داشبۆڕد</a></p>
</div>
</body>
</html>

