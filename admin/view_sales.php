<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('config.php');

$limit = 10;
$page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;  
$offset = ($page - 1) * $limit;

$filters = [];
$params = [];

if (!empty($_POST['product_name'])) {
    $filters[] = "o.product_name LIKE ?";
    $params[] = "%" . $_POST['product_name'] . "%";
}
if (!empty($_POST['transaction_id'])) {
    $filters[] = "o.transaction_id LIKE ?";
    $params[] = "%" . $_POST['transaction_id'] . "%";
}
if (!empty($_POST['user_name'])) {  // Add username filter
    $filters[] = "o.user_name LIKE ?";
    $params[] = "%" . $_POST['user_name'] . "%";
}
if (!empty($_POST['start_date'])) {
    $filters[] = "DATE(o.order_date) >= ?";
    $params[] = $_POST['start_date'];
}
if (!empty($_POST['end_date'])) {
    $filters[] = "DATE(o.order_date) <= ?";
    $params[] = $_POST['end_date'];
}

$whereSQL = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM orders o $whereSQL");
$totalStmt->execute($params);
$totalSales = $totalStmt->fetchColumn();
$totalPages = ceil($totalSales / $limit);

$sql = "
    SELECT o.order_id, o.order_date, o.product_name, o.user_name, o.order_price, o.transaction_id
    FROM orders o
    $whereSQL
    ORDER BY o.order_date DESC
    LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>تێڕوانینی فرۆشتنەکان</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="scc.css">
    <style>
        .dashboard {
            padding: 30px;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"], input[type="date"], button {
            padding: 10px;
            margin: 5px;
            border: 1px solid #aaa;
            border-radius: 5px;
        }

        button {
            background-color: #ff9900;
            color: #fff;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ff9900;
        }

        th {
            background: rgba(0,0,0,0.4);
            color: #ffc600;
        }

        tr:hover {
            background-color: #222;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #ff9900;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination a.disabled {
            pointer-events: none;
            background-color: #764700;
        }

        a.back-link {
            margin-top: 40px;
            display: inline-block;
            color: #fff;
            background: #d37207;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="dashboard">
    <h2>تێڕوانینی فرۆشتنەکان</h2>

   <form method="POST">
    <input type="text" name="product_name" placeholder="ناوی بەرهەم" value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>">
    <input type="text" name="transaction_id" placeholder="ID مامەڵە" value="<?= htmlspecialchars($_POST['transaction_id'] ?? '') ?>">
    <input type="text" name="user_name" placeholder="بەکارهێنەر" value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>">
    <input type="date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">
    <input type="date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
    <button type="submit">گەڕان</button>
</form>


    <table>
        <thead>
            <tr>
                <th>ئایدی داواکاری</th>
                <th>بەروار</th>
                <th>بەرهەم</th>
                <th>بەکارهێنەر</th>
                <th>ئایدی مامەڵە</th>
                <th>نرخ $</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($sales) > 0): ?>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= htmlspecialchars($sale['order_id']) ?></td>
                    <td><?= htmlspecialchars($sale['order_date']) ?></td>
                    <td><?= htmlspecialchars($sale['product_name']) ?></td>
                    <td><?= htmlspecialchars($sale['user_name']) ?></td>
                    <td><?= htmlspecialchars($sale['transaction_id']) ?></td>
                    <td><?= htmlspecialchars($sale['order_price']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">هیچ داواکاریەک نەدۆزرایەوە</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">« پێشوو</a>
        <?php else: ?>
            <a class="disabled">« پێشوو</a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">پاشتر »</a>
        <?php else: ?>
            <a class="disabled">پاشتر »</a>
        <?php endif; ?>
    </div>

    <a href="admin_dashboard.php" class="back-link">← گەڕانەوە بۆ داشبۆرد</a>
</div>
</body>
</html> 
