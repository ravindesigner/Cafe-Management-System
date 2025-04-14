<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('config.php');

$message = $error = "";
$imageDir = "/var/www/coffee.siciit.com/images/";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'] ?? '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $new_price = isset($_POST['new_price']) ? floatval($_POST['new_price']) : null;


    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM storage WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $message = "بەرهەم بە سەرکەوتووی سڕرایەوە.";
    } else {
       
        $stmt = $pdo->prepare("SELECT * FROM storage WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            $updated_quantity = $product['product_quantity'];
            if ($action === 'add') {
                $updated_quantity += $quantity;
            } elseif ($action === 'subtract') {
                $updated_quantity = max(0, $updated_quantity - $quantity);
            }

            $updateStmt = $pdo->prepare("UPDATE storage SET product_quantity = ? WHERE product_id = ?");
            $updateStmt->execute([$updated_quantity, $product_id]);

            if ($new_price !== null && $new_price >= 0) {
                $priceStmt = $pdo->prepare("UPDATE storage SET product_price = ? WHERE product_id = ?");
                $priceStmt->execute([$new_price, $product_id]);
            }

           
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $imageType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));

                if ($imageType === 'png') {
                    $productNameQuery = $pdo->prepare("SELECT product_name FROM storage WHERE product_id = ?");
                    $productNameQuery->execute([$product_id]);
                    $result = $productNameQuery->fetch();
                    if ($result) {
                        $productName = strtolower(trim($result['product_name']));
                        $imagePath = "/var/www/coffee.siciit.com/images/" . $productName . ".png";
                        move_uploaded_file($_FILES["product_image"]["tmp_name"], $imagePath);
                    }
                }
            } else {
                $message = "بەرهەم زیادکرا بە سەرکەوتووی";
            }
        } else {
            $error = "بەرهەم نەدۆزرایەوە";
        }
    }
}

//
$products = $pdo->query("SELECT * FROM storage")->fetchAll();


$product_to_edit = null;
if (isset($_GET['product_id'])) {
    $pid = $_GET['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM storage WHERE product_id = ?");
    $stmt->execute([$pid]);
    $product_to_edit = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>گۆڕانکاری بەرهەم</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard">
    <h2>گۆڕانکاری بەرهەم</h2>

    <?php if ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php elseif ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

  
    <form method="GET" action="add_product.php">
        <label>دیاریکردنی بەرهەم:</label>
        <select name="product_id" onchange="this.form.submit()">
            <option value="">-- دیاریکردنی بەرهەم --</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['product_id'] ?>" <?= isset($product_to_edit) && $product_to_edit['product_id'] == $prod['product_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prod['product_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($product_to_edit): ?>
        <form method="POST" action="add_product.php" enctype="multipart/form-data" style="margin-top: 20px; border: 1px solid #555; padding: 15px;">
            <input type="hidden" name="product_id" value="<?= $product_to_edit['product_id'] ?>">

            <p><strong><?= htmlspecialchars($product_to_edit['product_name']) ?></strong></p>
            <p>رێژەی ئێستا <?= $product_to_edit['product_quantity'] ?></p>
            <p>نرخی ئێستا $<?= number_format($product_to_edit['product_price'], 2) ?></p>

            <label>بڕی زیاد/کەم کردن:</label>
            <input type="number" name="quantity" min="0"><br>

            <label>نرخی نوێ (خوازیار)</label>
            <input type="number" name="new_price" min="0" step="0.01"><br>

            <label> (تەنها png) گۆڕینی وێنە</label>
            <input type="file" name="product_image" accept="image/png"><br><br>

            <button type="submit" name="action" value="add">➕ زیادکردن</button>
            <button type="submit" name="action" value="subtract">➖ کەمکردن</button>
            <button type="submit" name="action" value="delete" onclick="return confirm('ئایە دڵنیای لە سڕینەوەی بەرهەم؟')">🗑️ سڕینەوە</button>
        </form>
    <?php endif; ?>

    <p><a href="admin_dashboard.php">←گەڕانەوە بۆ داشبۆڕد </a></p>
</div>
</body>
</html>

