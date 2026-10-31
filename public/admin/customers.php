<?php
require_once '../../src/admin_auth.php';

// Fetch all customers
$customers = $pdo->query("SELECT * FROM customers ORDER BY registration_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers | Admin</title>
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
        
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="../index.php" class="logo">BRUV<span>.</span></a>
        <a href="dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Menu Items</a>
        <a href="categories.php">Categories</a>
        <a href="customers.php" class="active">Customers</a>
        <a href="reports.php">Reports</a>
        <a href="settings.php">Settings</a>
    </div>

    <div class="main-content">
        <h2>Registered Customers</h2>
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $cust): ?>
                        <tr>
                            <td>#<?= $cust['customer_id'] ?></td>
                            <td><?= htmlspecialchars($cust['first_name'] . ' ' . $cust['last_name']) ?></td>
                            <td><?= htmlspecialchars($cust['email']) ?></td>
                            <td><?= htmlspecialchars($cust['phone'] ?: 'N/A') ?></td>
                            <td><?= date('d M Y', strtotime($cust['registration_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>