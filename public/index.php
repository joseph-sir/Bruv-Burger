<?php
require_once '../src/config.php';

// Fetch all available products from the database
$stmt = $pdo->query("SELECT * FROM products WHERE is_available = 1");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bruv Burger Boksburg | Smashburgers & Loaded Fries</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">BRUV<span>.</span></a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <?php if(isset($_SESSION['customer_id'])): ?>
                        <li><a href="my_orders.php">My Orders</a></li>
                        <li><a href="my_orders.php?logout=true" style="color: #ffcccc;">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                    <li><a href="cart.php" class="cart-link">Cart (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <h1>Smashburgers & <span>Loaded Fries</span></h1>
            <p>Boksburg's home of double-stacked beef burgers, dunked wings and loaded boxes.</p>
            <a href="#menu" class="btn">View Menu</a>
        </div>
    </section>

    <!-- MENU -->
    <div class="container" id="menu">
        <h2 class="section-title">Our Menu</h2>
        <div class="menu-grid">
            <?php foreach ($products as $product): ?>
                <div class="menu-item">
                    <!-- Loads the image from your uploads folder -->
                    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    
                    <div class="item-info">
                        <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <div class="price">R<?= number_format($product['price'], 2) ?></div>
                        
                        <!-- Add to Cart Form -->
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <p>© 2026 Bruv Burger Boksburg. Halal Certified.</p>
            <p>BP Filling Station, North Rand Rd | Open Daily 10:30 - 21:30</p>
            <!-- Subtle staff link -->
            <p style="margin-top: 15px; font-size: 11px; opacity: 0.5;">
                <a href="admin/login.php" style="color: #8ba0b8; text-decoration: none;">Staff Portal</a>
            </p>
        </div>
    </footer>