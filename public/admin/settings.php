<?php
require_once '../../src/admin_auth.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    
    // Get current admin data
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$_SESSION['admin_username']]);
    $admin = $stmt->fetch();
    
    if (password_verify($current_pass, $admin['password_hash'])) {
        if (strlen($new_pass) >= 6) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE admin_id = ?");
            $update->execute([$new_hash, $admin['admin_id']]);
            $message = "<div style='color:green; margin-bottom:15px;'>Password updated successfully!</div>";
        } else {
            $message = "<div style='color:red; margin-bottom:15px;'>New password must be at least 6 characters.</div>";
        }
    } else {
        $message = "<div style='color:red; margin-bottom:15px;'>Current password is incorrect.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | Admin</title>
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
        
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { padding: 10px 20px; background: #0044CC; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 10px; }
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
        <a href="reports.php">Reports</a>
        <a href="settings.php" class="active">Settings</a>
    </div>

    <div class="main-content">
        <h2>Account Settings</h2>
        
        <div class="card" style="margin-top: 30px;">
            <h3>Change Password</h3>
            <?= $message ?>
            <form method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <button type="submit" class="btn">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>