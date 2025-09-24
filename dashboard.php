<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require_once 'db.php';
 
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
 
// For seller: show products
if ($role === 'seller') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ?");
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // For buyer: show quotations or orders
    $stmt = $pdo->prepare("SELECT q.*, p.name as product_name FROM quotations q JOIN products p ON q.product_id = p.id WHERE buyer_id = ?");
    $stmt->execute([$user_id]);
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; }
        header { background: #0066cc; color: white; padding: 1rem; text-align: center; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .section { background: white; margin-bottom: 2rem; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; }
        .card { padding: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #ff6600; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #e55a00; }
        .status { padding: 0.25rem 0.5rem; border-radius: 3px; color: white; }
        .pending { background: #ffc107; color: black; }
        .accepted { background: #28a745; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($role); ?>)</h1>
        <a href="#" onclick="window.location.href='index.php'" style="color: white;">Home</a> | <a href="#" onclick="logout()" style="color: white;">Logout</a>
    </header>
    <div class="container">
        <?php if ($role === 'seller'): ?>
            <div class="section">
                <h2>Your Products</h2>
                <a href="#" onclick="window.location.href='add_product.php'"><button>Add Product</button></a>
                <div class="grid">
                    <?php foreach ($products as $product): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p>Price: $<?php echo $product['price']; ?> | MOQ: <?php echo $product['moq']; ?></p>
                            <button onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="section">
                <h2>Inquiries & Messages</h2>
                <a href="#" onclick="window.location.href='messages.php'"><button>View Messages</button></a>
            </div>
        <?php else: ?>
            <div class="section">
                <h2>Your Quotations</h2>
                <div class="grid">
                    <?php foreach ($quotations as $q): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($q['product_name']); ?></h3>
                            <p>Qty: <?php echo $q['quantity']; ?> | Proposed: $<?php echo $q['proposed_price']; ?></p>
                            <span class="status <?php echo $q['status']; ?>"><?php echo ucfirst($q['status']); ?></span>
                            <button onclick="negotiate(<?php echo $q['id']; ?>)">Negotiate</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="section">
                <h2>Your Orders</h2>
                <a href="#" onclick="window.location.href='orders.php'"><button>View Orders</button></a>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function logout() {
            fetch('logout.php', { method: 'POST' }).then(() => window.location.href = 'index.php');
        }
        function editProduct(id) { window.location.href = `edit_product.php?id=${id}`; }
        function deleteProduct(id) {
            if (confirm('Delete?')) {
                fetch('delete_product.php', { method: 'POST', body: new FormData().append('id', id) }).then(() => location.reload());
            }
        }
        function negotiate(id) { window.location.href = `quotation_detail.php?id=${id}`; }
    </script>
</body>
</html>
