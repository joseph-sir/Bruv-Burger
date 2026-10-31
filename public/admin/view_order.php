<?php
require_once '../../src/admin_auth.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order and customer info
$stmt = $pdo->prepare("SELECT o.*, c.first_name, c.last_name, c.email, c.phone 
                       FROM orders o 
                       JOIN customers c ON o.customer_id = c.customer_id 
                       WHERE o.order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$stmt = $pdo->prepare("SELECT oi.*, p.product_name 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $updateStmt->execute([$new_status, $order_id]);
    header("Location: view_order.php?id=" . $order_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .order-info { display: flex; justify-content: space-between; margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 8px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: var(--bruv-dark); color: white; }
        .status-form { margin-top: 20px; text-align: right; }
        select { padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        .btn-update { padding: 8px 15px; background: var(--bruv-blue); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Order #<?= $order_id ?></h2>
        <a href="dashboard.php" style="color: var(--bruv-blue);">&larr; Back to Dashboard</a>

        <div class="order-info" style="margin-top: 15px;">
            <div>
                <strong>Customer:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
                <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?>
            </div>
            <div>
                <strong>Date:</strong> <?= date('d M Y, H:i', strtotime($order['order_date'])) ?><br>
                <strong>Type:</strong> <?= htmlspecialchars($order['order_type']) ?><br>
                <strong>Total:</strong> R<?= number_format($order['total_amount'], 2) ?>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
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
            </tbody>
        </table>

        <div class="status-form">
            <form method="POST">
                <strong>Update Status: </strong>
                <select name="status">
                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Preparing" <?= $order['status'] == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                    <option value="Ready" <?= $order['status'] == 'Ready' ? 'selected' : '' ?>>Ready</option>
                    <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn-update">Update</button>
            </form>
        </div>
    </div>
</body>
</html>