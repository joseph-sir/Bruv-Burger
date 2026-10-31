<?php
require_once '../../src/admin_auth.php';

// 1. Total Revenue (Completed / Not Cancelled)
$rev = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?: 0;

// 2. Total Orders
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// 3. Top Selling Item
$top_item = $pdo->query("SELECT p.product_name, SUM(oi.quantity) as total_sold 
                         FROM order_items oi 
                         JOIN products p ON oi.product_id = p.product_id 
                         GROUP BY p.product_id 
                         ORDER BY total_sold DESC LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Sidebar Layout */
        body { display: flex; background: #f0f2f5; font-family: 'Montserrat', sans-serif; }
        .sidebar { width: 250px; height: 100vh; background: #001a4d; color: white; position: fixed; padding: 20px 0; }
        .sidebar .logo { font-size: 24px; font-weight: 900; text-align: center; padding-bottom: 30px; color: white; text-decoration: none; display: block; }
        .sidebar .logo span { color: #FFB703; }
        .sidebar a { display: block; padding: 15px 25px; color: #8ba0b8; text-decoration: none; font-weight: 600; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: #0044CC; color: white; border-left: 4px solid #FFB703; }
        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .stat-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card h4 { color: #888; font-size: 14px; text-transform: uppercase; margin-bottom: 15px; }
        .stat-card .number { font-size: 36px; font-weight: 800; color: #0044CC; }
        .stat-card .text-val { font-size: 24px; font-weight: 800; color: #27ae60; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="../index.php" class="logo">BRUV<span>.</span></a>
        <a href="dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Menu Items</a>
        <a href="categories.php">Categories</a>
        <a href="customers.php">Customers</a>
        <a href="reports.php" class="active">Reports</a>
        <a href="settings.php">Settings</a>
    </div>

    <div class="main-content">
        <h2>Business Reports</h2>
        
        <div class="stats-grid" style="margin-top: 30px;">
            <div class="stat-card">
                <h4>Total Revenue (All Time)</h4>
                <div class="number">R<?= number_format($rev, 2) ?></div>
            </div>
            
            <div class="stat-card">
                <h4>Total Orders Placed</h4>
                <div class="number"><?= $total_orders ?></div>
            </div>

            <div class="stat-card">
                <h4>Best Selling Item</h4>
                <div class="text-val">
                    <?= $top_item ? htmlspecialchars($top_item['product_name']) : 'No orders yet' ?>
                </div>
                <?php if($top_item): ?>
                    <p style="color:#888; margin-top:10px;"><?= $top_item['total_sold'] ?> sold</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>