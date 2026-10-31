<?php
require_once '../src/config.php';

// Protect the page: Must be logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_name']);
    header("Location: index.php");
    exit;
}

// Fetch this customer's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->execute([$_SESSION['customer_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders | Bruv Burger</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .orders-container { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .order-card { border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; color: white; text-transform: uppercase; }
        .status-Pending { background: #f39c12; }
        .status-Preparing { background: #3498db; }
        .status-Ready { background: #27ae60; }
        .status-Completed { background: #7f8c8d; }
        .status-Cancelled { background: #e74c3c; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">BRUV<span>.</span></a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Menu</a></li>
                    <li><a href="my_orders.php">My Orders</a></li>
                    <li><a href="?logout=true" style="color: #ffcccc;">Logout (<?= htmlspecialchars($_SESSION['customer_name']) ?>)</a></li>
                    <li><a href="cart.php" class="cart-link">Cart (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="orders-container">
        <h2 style="color: var(--bruv-dark); margin-bottom: 20px;">My Orders</h2>
        
        <?php if (empty($orders)): ?>
            <p>You haven't placed any orders yet. <a href="index.php" style="color:var(--bruv-blue);">Order something delicious!</a></p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div>
                        <h3 style="margin-bottom: 5px;">Order #<?= $order['order_id'] ?></h3>
                        <p style="color: #666; font-size: 14px;">
                            Date: <?= date('d M Y, H:i', strtotime($order['order_date'])) ?><br>
                            Total: <strong>R<?= number_format($order['total_amount'], 2) ?></strong>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge status-<?= $order['status'] ?>">
                            <?= $order['status'] ?>
                        </span>
                        <br><br>
                        <a href="order_details.php?id=<?= $order['order_id'] ?>" style="color: var(--bruv-blue); text-decoration: none; font-size: 14px;">View Details &rarr;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>