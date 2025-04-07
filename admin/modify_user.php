<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('config.php');


if (isset($_POST['update'])) {
    $id         = $_POST['user_id'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $is_admin   = isset($_POST['is_admin']) ? 1 : 0;

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, first_name=?, last_name=?, password=?, is_admin=? WHERE id=?");
        $stmt->execute([$username, $email, $first_name, $last_name, $password, $is_admin, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, first_name=?, last_name=?, is_admin=? WHERE id=?");
        $stmt->execute([$username, $email, $first_name, $last_name, $is_admin, $id]);
    }

    $message = "بەکارهێنەر نوێکرایەوە بە سەرکەوتوویی";
}


if (isset($_POST['delete'])) {
    $id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $message = "بەکارهێنەر سڕرایەوە ";
}


$users = $pdo->query("SELECT id, username FROM users")->fetchAll();


$user_to_edit = null;
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $user_to_edit = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user_to_edit->execute([$user_id]);
    $user_to_edit = $user_to_edit->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>گۆڕانکاری بەکارهێنەران</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard">
    <h2>گۆڕانکاری بەکارهێنەران</h2>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

   
    <form method="GET" action="modify_user.php">
        <label>دیاریکردنی بەکارهێنەر:</label>
        <select name="user_id" onchange="this.form.submit()">
            <option value="">-- دیارکردن --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= isset($user_to_edit) && $user_to_edit['id'] == $user['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($user_to_edit): ?>
        
        <form method="POST" action="modify_user.php" style="margin-bottom: 30px; border: 1px solid #555; padding: 15px;">
            <input type="hidden" name="user_id" value="<?= $user_to_edit['id'] ?>">

            <label>ناوی بەکارهێنەر:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user_to_edit['username']) ?>" required><br>

            <label>پۆستی ئەلکترۆنی:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user_to_edit['email']) ?>" required><br>

            <label>ناوی یەکەم:</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user_to_edit['first_name']) ?>" required><br>

            <label>ناوی کۆتا:</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user_to_edit['last_name']) ?>" required><br>

            <label>ووشەی نهێنی (بەتاڵ جێی بهێلە بۆ نەگۆڕین)</label>
            <input type="password" name="password"><br>

            <label><input type="checkbox" name="is_admin" <?= $user_to_edit['is_admin'] ? 'checked' : '' ?>> ئەدمین</label><br><br>

            <button type="submit" name="update">نوێکردنەوەی بەکارهێنەر</button>
            <button type="submit" name="delete" onclick="return confirm('ئایە دڵنیای دەتەوێت ئەم بسڕیتەوە؟')">سڕینەوەی بەکارهێنەر</button>
        </form>
    <?php endif; ?>

    <p><a href="admin_dashboard.php">← گەڕانەوە بۆ داشبۆڕد</a></p>
</div>
</body>
</html>

