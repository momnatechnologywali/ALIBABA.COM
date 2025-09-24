<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require_once 'db.php';
 
$user_id = $_SESSION['user_id'];
$success = $error = '';
 
if ($_POST) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $moq = intval($_POST['moq']);
    $images = []; // Handle upload
    if (isset($_FILES['images']) && $_FILES['images']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file = $_FILES['images']['tmp_name'];
        $filename = uniqid() . '_' . basename($_FILES['images']['name']);
        if (move_uploaded_file($file, $upload_dir . $filename)) {
            $images = [$upload_dir . $filename];
        }
    }
    $images_json = json_encode($images);
 
    if (empty($name) || empty($category) || $price <= 0) {
        $error = 'Invalid input.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, description, category, price, moq, images) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $name, $description, $category, $price, $moq, $images_json])) {
            $success = 'Product added!';
            echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);</script>";
        } else {
            $error = 'Failed to add.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 1rem; color: #0066cc; }
        input, textarea, select { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 0.75rem; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #004499; }
        .error { color: red; text-align: center; margin-bottom: 1rem; }
        .success { color: green; text-align: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <input type="text" name="category" placeholder="Category (e.g., Apparel)" required>
            <input type="number" name="price" placeholder="Price per unit" step="0.01" required>
            <input type="number" name="moq" placeholder="Minimum Order Quantity" required>
            <input type="file" name="images" accept="image/*" multiple>
            <button type="submit">Add Product</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="#" onclick="window.location.href='dashboard.php'">Back to Dashboard</a></p>
    </div>
</body>
</html>
