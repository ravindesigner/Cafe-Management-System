<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$products = [];
$error = '';
$success = '';


$stmt = $pdo->query("SELECT * FROM storage");
$products = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

 
    $stmt = $pdo->prepare("SELECT * FROM storage WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product && $product['product_quantity'] >= $quantity) {
        $new_qty = $product['product_quantity'] - $quantity;
        $order_price = $product['product_price'] * $quantity;
        $order_date = date('Y-m-d H:i:s');
        $transaction_id = uniqid('tx_');

    
        $insert = $pdo->prepare("INSERT INTO orders (order_date, product_name, user_name, transaction_id, order_price)
                                 VALUES (?, ?, ?, ?, ?)");
        $insert->execute([
            $order_date,
            $product['product_name'],
            $username,
            $transaction_id,
            $order_price
        ]);


        $update = $pdo->prepare("UPDATE storage SET product_quantity = ? WHERE product_id = ?");
        $update->execute([$new_qty, $product_id]);

        $success = "داواکاری بەسەرکەوتووی دانرا  {$quantity} x {$product['product_name']}.";
    } else {
        $error = "رێژەی پێویست بەردەست نییە لە کۆگا";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>سازکردنی داواکاری</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 20px;
            margin: 30px auto;
            max-width: 800px;
        }
        .product-card {
            background: #1a1a1a;
            border: 2px solid #ff9885;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 0 10px #ffdfb6;
        }
        .product-card img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="dashboard-page">
    <div class="dashboard-box">
        <h2>سازکردنی داواکاری نوێ</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

        <form method="POST">
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <label class="product-card">
                        <input type="radio" name="product_id" value="<?= $product['product_id'] ?>" required>
                        <img src="images/<?= $product['product_name'] ?>.png" alt="<?= $product['product_name'] ?>">
                        <div><strong><?= ucfirst(str_replace('_', ' ', $product['product_name'])) ?></strong></div>
                        <div>ڕێژە: <?= $product['product_quantity'] ?></div>
                        <div>نرخ: $<?= $product['product_price'] ?></div>
                    </label>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 20px;">
                <input type="number" name="quantity" placeholder="ڕێژە" min="1" required><br>
                <button type="submit"> پشتڕاستکردنەوەی داواکاری </button>
            </div>
        </form>

        <a href="dashboard.php" class="btn">← گەڕانەوە بۆ داشبۆرد</a>
    </div>
</body>
</html>

