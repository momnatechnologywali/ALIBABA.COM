<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require_once 'db.php';
 
$id = intval($_GET['id']);
$qty = intval($_GET['qty'] ?? 1);
 
$stmt = $pdo->prepare("SELECT * FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$buyer_id = $_SESSION['user_id'];
$seller_id = $product['id']; // Seller ID from product
 
$success = $error = '';
 
if ($_POST) {
    $proposed_price = floatval($_POST['proposed_price']);
    $notes = trim($_POST['notes']);
 
    $stmt = $pdo->prepare("INSERT INTO quotations (product_id, buyer_id, seller_id, quantity, proposed_price, negotiation_notes) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$id, $buyer_id, $seller_id, $qty, $proposed_price, $notes])) {
        $success = 'Quotation requested! Supplier will respond.';
        echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);</script>";
    } else {
        $error = 'Failed to request.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Quotation - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #0066cc; margin-bottom: 1rem; }
        input, textarea { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 0.75rem; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error, .success { text-align: center; margin-bottom: 1rem; }
        .error { color: red; } .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Request Quotation for <?php echo htmlspecialchars($product['name']); ?></h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <p>Quantity: <?php echo $qty; ?></p>
            <p>Base Price: $<?php echo $product['price']; ?></p>
            <input type="number" name="proposed_price" placeholder="Your proposed price per unit" step="0.01" required>
            <textarea name="notes" placeholder="Additional notes or negotiation terms" rows="4"></textarea>
            <button type="submit">Send Request</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="#" onclick="window.location.href='product_detail.php?id=<?php echo $id; ?>'">Back to Product</a></p>
    </div>
</body>
</html>
