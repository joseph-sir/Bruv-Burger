<?php
require_once '../src/config.php';

// 1. Handle "Add to Cart" form submission
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Increase quantity if item already in cart, else add it
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    
    // Redirect back to cart page to prevent form resubmission
    header("Location: cart.php");
    exit;
}

// 2. Handle "Remove from Cart"
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart | Bruv Burger</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Extra styles for the cart table */
        .cart-table { width: 100%; border-collapse: collapse; background: white; margin-top: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .cart-table th, .cart-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .cart-table th { background: var(--bruv-dark); color: white; }
        .cart-total { text-align: right; margin-top: 20px; font-size: 24px; font-weight: bold; }
        .checkout-btn { margin-top: 20px; float: right; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">BRUV<span>.</span></a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cart.php" class="cart-link">Cart (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2 class="section-title">Your Order</h2>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <p style="text-align:center; margin-top:50px;">Your cart is empty. <a href="index.php">Go back to the menu</a></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    // Fetch details for each item in the session cart
                    foreach ($_SESSION['cart'] as $id => $quantity): 
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
                        $stmt->execute([$id]);
                        $item = $stmt->fetch();
                        
                        if ($item) {
                            $subtotal = $item['price'] * $quantity;
                            $grand_total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>R<?= number_format($item['price'], 2) ?></td>
                            <td><?= $quantity ?></td>
                            <td>R<?= number_format($subtotal, 2) ?></td>
                            <td><a href="cart.php?remove=<?= $id ?>" style="color:red; text-decoration:none;">Remove</a></td>
                        </tr>
                    <?php } endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-total">
                Total: R<?= number_format($grand_total, 2) ?>
            </div>
            
            <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
            <div style="clear:both;"></div>
        <?php endif; ?>
    </div>
</body>
</html>