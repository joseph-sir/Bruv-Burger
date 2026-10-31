<?php
require_once '../src/config.php';

// If the cart is empty, send them back to the menu
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = false;

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $order_type = $_POST['order_type'];

    // If the customer is logged in, use their ID. Otherwise, do guest checkout.
    if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
    } else {
        // ... [Keep the existing logic that checks if the email exists, or creates a new customer] ...
        $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch();

        if ($customer) {
            $customer_id = $customer['customer_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $phone, password_hash('guest123', PASSWORD_DEFAULT)]);
            $customer_id = $pdo->lastInsertId();
        }
    }

    // 1. Basic Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // 2. Start the Transaction
            $pdo->beginTransaction();

            // 3. Insert or find the Customer
            $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();

            if ($customer) {
                $customer_id = $customer['customer_id'];
            } else {
                // Create new customer (using a dummy password for guest checkout)
                $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $phone, password_hash('guest123', PASSWORD_DEFAULT)]);
                $customer_id = $pdo->lastInsertId();
            }

            // 4. Calculate Total (Never trust the client, always calculate on the server)
            $total_amount = 0;
            $order_items = [];
            
            foreach ($_SESSION['cart'] as $id => $quantity) {
                $stmt = $pdo->prepare("SELECT price, product_name FROM products WHERE product_id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch();
                
                if ($product) {
                    $subtotal = $product['price'] * $quantity;
                    $total_amount += $subtotal;
                    $order_items[] = [
                        'id' => $id, 
                        'quantity' => $quantity, 
                        'price' => $product['price'], 
                        'subtotal' => $subtotal
                    ];
                }
            }

            // 5. Create the Order
            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, order_type) VALUES (?, ?, 'Pending', ?)");
            $stmt->execute([$customer_id, $total_amount, $order_type]);
            $order_id = $pdo->lastInsertId();

            // 6. Insert Order Items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($order_items as $item) {
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $item['subtotal']]);
            }

            // 7. Create a Payment Record (Pending)
            $stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, payment_method, status) VALUES (?, ?, 'Card', 'Pending')");
            $stmt->execute([$order_id, $total_amount]);

            // 8. Commit the Transaction (Save everything)
            $pdo->commit();

            // 9. Clear the cart
            unset($_SESSION['cart']);
            $success = true;
            
            // Redirect to success page
            header("Location: success.php?order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            // If anything fails, rollback the database changes
            $pdo->rollBack();
            $error = "Something went wrong processing your order. Please try again.";
            // In a real app, you would log $e->getMessage() here
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Bruv Burger</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkout-container { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: var(--bruv-dark); }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .pay-btn { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .pay-btn:hover { background: #219a52; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">BRUV<span>.</span></a>
        </div>
    </header>

    <div class="checkout-container">
        <h2 style="color: var(--bruv-dark); margin-bottom: 20px; text-align:center;">Checkout</h2>
        
        <?php if ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="checkout.php">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number *</label>
                <input type="text" name="phone" required>
            </div>
            <div class="form-group">
                <label>Order Type</label>
                <select name="order_type">
                    <option value="Takeaway">Takeaway (Collect at BP Station)</option>
                    <option value="Dine-In">Dine-In</option>
                </select>
            </div>
            
            <button type="submit" class="pay-btn">Place Order (Pay on Collection)</button>
        </form>
    </div>
</body>
</html>