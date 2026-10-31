<?php
require_once '../../src/admin_auth.php';

// --- DYNAMIC STATISTICS ---

// 1. Today's Orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()");
$todays_orders = $stmt->fetchColumn();

// 2. Revenue Today
$stmt = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = CURDATE() AND status != 'Cancelled'");
$revenue_today = $stmt->fetchColumn() ?: 0;

// 3. Pending Orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'");
$pending_orders = $stmt->fetchColumn();

// 4. Total Menu Items
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$menu_items = $stmt->fetchColumn();

// 5. Fetch Recent Orders
$stmt = $pdo->query("SELECT o.order_id, o.total_amount, o.status, o.order_date, c.first_name, c.last_name 
                     FROM orders o 
                     LEFT JOIN customers c ON o.customer_id = c.customer_id 
                     ORDER BY o.order_date DESC LIMIT 5");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Bruv Burger Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Admin Sidebar Layout */
        body { display: flex; background: #f0f2f5; font-family: 'Montserrat', sans-serif; }
        
        .sidebar { width: 250px; height: 100vh; background: #001a4d; color: white; position: fixed; padding: 20px 0; }
        .sidebar .logo { font-size: 24px; font-weight: 900; text-align: center; padding-bottom: 30px; letter-spacing: 2px; color: white; text-decoration: none; display: block; }
        .sidebar .logo span { color: #FFB703; }
        .sidebar a { display: block; padding: 15px 25px; color: #8ba0b8; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #0044CC; color: white; border-left: 4px solid #FFB703; }

        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-bar h2 { color: #001a4d; margin: 0; }
        .user-info { color: #666; font-size: 14px; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stat-card h4 { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .stat-card .number { font-size: 32px; font-weight: 800; color: #0044CC; }
        .stat-card .number.green { color: #27ae60; }
        .stat-card .number.red { color: #E63946; }

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-container h3 { margin-bottom: 20px; color: #001a4d; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { color: #001a4d; font-size: 12px; text-transform: uppercase; }
        
        .status { padding: 5px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; color: white; }
        .status-Pending { background: #f39c12; }
        .status-Preparing { background: #3498db; }
        .status-Ready { background: #27ae60; }
        .status-Completed { background: #7f8c8d; }
        .status-Cancelled { background: #e74c3c; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="../index.php" class="logo">BRUV<span>.</span></a>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">Orders</a>
        <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">Menu Items</a>
        <a href="categories.php" class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">Categories</a>
        <a href="customers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : '' ?>">Customers</a>
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">Reports</a>
        <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">Settings</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="header-bar">
            <h2>Admin Dashboard</h2>
            <div class="user-info">Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?> | <a href="logout.php" style="color: #E63946; text-decoration: none;">Logout</a></div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Today's Orders</h4>
                <div class="number"><?= $todays_orders ?></div>
            </div>
            <div class="stat-card">
                <h4>Revenue Today</h4>
                <div class="number green">R<?= number_format($revenue_today, 2) ?></div>
            </div>
            <div class="stat-card">
                <h4>Pending Orders</h4>
                <div class="number red"><?= $pending_orders ?></div>
            </div>
            <div class="stat-card">
                <h4>Menu Items</h4>
                <div class="number"><?= $menu_items ?></div>
            </div>
        </div>

        <!-- RECENT ORDERS TABLE -->
        <div class="table-container">
            <h3>Recent Orders</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" style="text-align:center;">No orders yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></td>
                                <td>R<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <span class="status status-<?= $order['status'] ?>">
                                        <?= $order['status'] ?>
                                    </span>
                                </td>
                                <td><a href="view_order.php?id=<?= $order['order_id'] ?>" style="color:#0044CC;">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>