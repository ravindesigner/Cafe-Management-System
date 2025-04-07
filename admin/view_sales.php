<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('config.php');


$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;


$totalStmt = $pdo->query("SELECT COUNT(*) FROM orders");
$totalSales = $totalStmt->fetchColumn();
$totalPages = ceil($totalSales / $limit);


$stmt = $pdo->prepare("
    SELECT o.order_id, o.order_date, o.product_name, o.user_name, o.order_price, o.transaction_id
    FROM orders o
    ORDER BY o.order_date DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$sales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>تێڕوانینی فرۆشتنەکان</title>
    <link rel="stylesheet" href="style.css">
    <style>
       
        .dashboard {
            padding: 30px;
        }

        h2 {
            margin-bottom: 20px;
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
            background: #d37207;;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
    
    </style>
</head>
<body>
<div class="dashboard">
    <h2>تێڕوانینی فرۆشتنەکان</h2>

    <table>
        <thead>
            <tr>
                <th>ئایدی داواکاری</th>
                <th>بەروار</th>
                <th>بەرهەم</th>
                <th>بەکارهێنەر</th>
                <th> ئایدی مامەڵە</th>
                <th>نرخ $</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>

    
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>"> « پێشوو </a>
        <?php else: ?>
            <a class="disabled"> « پێشوو </a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>"> پاشتر  »</a>
        <?php else: ?>
            <a class="disabled"> پاشتر  »</a>
        <?php endif; ?>
    </div>

    <a href="admin_dashboard.php" class="back-link">← گەڕانەوە بۆ داشبۆرد</a>
</div>
</body>
</html>
