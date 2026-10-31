<?php
require_once '../../src/admin_auth.php';

$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    
    // --- SECURE IMAGE UPLOAD LOGIC ---
    $image_name = 'default.jpg'; // Fallback image
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        
        // 1. Validate File Size (Max 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            $message = "Error: Image is too large. Max 5MB.";
        } else {
            // 2. Validate actual MIME type (Not just the extension)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file_tmp);
            finfo_close($finfo);
            
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (!in_array($mime, $allowed_mimes)) {
                $message = "Error: Only JPG, PNG, and WEBP images are allowed.";
            } else {
                // 3. Generate a safe, unique file name
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $safe_name = strtolower(str_replace(' ', '-', $name));
                $new_filename = $safe_name . '-' . time() . '.' . $ext;
                
                // 4. Move file to uploads folder
                $upload_path = '../../public/uploads/' . $new_filename;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_name = $new_filename;
                } else {
                    $message = "Error: Failed to save image.";
                }
            }
        }
    }

    // Insert into database if no image errors occurred
    if (empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO products (category_id, product_name, description, price, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$category_id, $name, $description, $price, $image_name])) {
            $message = "Product added successfully!";
        }
    }
}

// Fetch categories for the dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
// Fetch all products to list below
$products = $pdo->query("SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { max-width: 1000px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { padding: 12px; background: #27ae60; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .table th, .table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #f4f4f4; }
        .msg { padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Manage Menu Items</h2>
        <a href="dashboard.php" style="color: var(--bruv-blue);">&larr; Back to Dashboard</a>

        <?php if ($message): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- ADD PRODUCT FORM -->
        <h3 style="margin-top: 20px;">Add New Product</h3>
        <form method="POST" enctype="multipart/form-data"> <!-- MUST have enctype for file uploads -->
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" required>
                </div>
                <div class="form-group">
                    <label>Price (R)</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Upload Image (JPG/PNG)</label>
                    <input type="file" name="image" accept="image/jpeg, image/png, image/webp">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" required></textarea>
            </div>
            <button type="submit" name="add_product" class="btn">Add Product</button>
        </form>

        <hr style="margin: 40px 0;">

        <!-- PRODUCT LIST -->
        <h3>Current Menu</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="../uploads/<?= htmlspecialchars($p['image']) ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;"></td>
                        <td><?= htmlspecialchars($p['product_name']) ?></td>
                        <td><?= htmlspecialchars($p['category_name']) ?></td>
                        <td>R<?= number_format($p['price'], 2) ?></td>
                        <td><a href="#" style="color:red;">Delete</a></td> <!-- Delete logic can be added later -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>