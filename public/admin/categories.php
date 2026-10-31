<?php
require_once '../../src/admin_auth.php';

// Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->execute([$name]);
    }
}

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories | Admin</title>
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
        
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-inline { display: flex; gap: 10px; }
        .form-inline input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="../index.php" class="logo">BRUV<span>.</span></a>
        <a href="dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Menu Items</a>
        <a href="categories.php" class="active">Categories</a>
        <a href="customers.php">Customers</a>
        <a href="reports.php">Reports</a>
        <a href="settings.php">Settings</a>
    </div>

    <div class="main-content">
        <h2>Manage Categories</h2>
        
        <div class="card">
            <h3>Add New Category</h3>
            <form method="POST" class="form-inline" style="margin-top: 15px;">
                <input type="text" name="category_name" placeholder="Category Name (e.g., Smash Burgers)" required>
                <button type="submit" name="add_category" class="btn">Add Category</button>
            </form>
        </div>

        <div class="card">
            <h3>Existing Categories</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['category_id'] ?></td>
                            <td><?= htmlspecialchars($cat['category_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>