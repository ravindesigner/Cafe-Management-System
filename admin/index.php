<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND is_admin = 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = true;

        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>چوونەژوورەوەی ئەدمین</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>چوونەژوورەوەی ئەدمین</h2>
        <form method="POST" action="index.php">
            <label for="username">ناوی بەکارهێنەر:</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">ووشەی نهێنی:</label>
            <input type="password" name="password" id="password" required>
            <br>
            <button type="submit">چوونەژوورەوە</button>
        </form>

        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
