<?php require_once '../src/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful | Bruv Burger</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-box { text-align: center; max-width: 500px; margin: 100px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .success-icon { font-size: 60px; color: #27ae60; margin-bottom: 20px; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">BRUV<span>.</span></a>
        </div>
    </header>

    <div class="success-box">
        <div class="success-icon">✓</div>
        <h2>Order Placed Successfully!</h2>
        <p style="margin: 15px 0; color: #666;">
            Thank you for your order. Your order number is <strong>#<?= htmlspecialchars($_GET['order_id'] ?? 'N/A') ?></strong>.
        </p>
        <p style="margin-bottom: 25px; color: #666;">We will start preparing your food shortly.</p>
        <a href="index.php" class="btn">Back to Home</a>
    </div>
</body>
</html>