<?php
require_once '../src/config.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customer_id = $_SESSION['customer_id'];

// SECURITY: Only fetch the order if it belongs to the logged-in customer
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$order_id, $customer_id]);
$order = $stmt->fetch();

if (!$order) {
    die("<h2 style='text-align:center; margin-top:50px;'>Order not found or unauthorized access.</h2>");
}

// Fetch the items for this order
$stmt = $pdo->prepare("SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> | Bruv Burger</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .order-container { max-width: 700px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: var(--bruv-dark); color: white; }
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
                    <li><a href="my_orders.php?logout=true" style="color: #ffcccc;">Logout</a></li>
                    <li><a href="cart.php" class="cart-link">Cart (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="order-container">
        <h2>Order #<?= $order_id ?> Details</h2>
        <p><strong>Date:</strong> <?= date('d M Y, H:i', strtotime($order['order_date'])) ?></p>
        <p><strong>Status:</strong> <?= $order['status'] ?></p>
        <p><strong>Type:</strong> <?= $order['order_type'] ?></p>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>R<?= number_format($item['unit_price'], 2) ?></td>
                        <td>R<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:bold;">Total:</td>
                    <td style="font-weight:bold;">R<?= number_format($order['total_amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>
        <a href="my_orders.php" style="display:inline-block; margin-top:20px; color:var(--bruv-blue);">&larr; Back to My Orders</a>
    </div>
</body>
</html>