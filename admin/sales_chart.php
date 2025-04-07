<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

include('config.php');


$stmt = $pdo->query("
    SELECT DATE(order_date) as date, SUM(order_price) as total_sales
    FROM orders
    GROUP BY DATE(order_date)
    ORDER BY DATE(order_date)
");
$data = $stmt->fetchAll();

$labels = [];
$totals = [];
foreach ($data as $row) {
    $labels[] = $row['date'];
    $totals[] = $row['total_sales'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>نەخشەی فرۆشتنەکان</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard">
        <h2>فرۆشتنەکان بەپێێ کات</h2>
        <canvas id="salesChart" width="800" height="400"></canvas>
        <p><a href="admin_dashboard.php">گەڕانەوە بۆ داشبۆڕد</a></p>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: <?= json_encode($totals) ?>,
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales ($)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

