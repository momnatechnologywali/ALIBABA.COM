<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require_once 'db.php';
 
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
 
// For buyer: own orders
if ($role === 'buyer') {
    $stmt = $pdo->prepare("SELECT o.*, p.name as product_name, u.company_name as seller FROM orders o JOIN products p ON o.product_id = p.id JOIN users u ON o.seller_id = u.id WHERE o.buyer_id = ?");
} else {
    // For seller: orders for their products
    $stmt = $pdo->prepare("SELECT o.*, p.name, u.company_name as buyer FROM orders o JOIN products p ON o.product_id = p.id JOIN users u ON o.buyer_id = u.id WHERE o.seller_id = ?");
}
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Dummy payment
if (isset($_POST['pay_order'])) {
    $order_id = intval($_POST['order_id']);
    $total = floatval($_POST['total']);
    // Simulate payment
    $payment_info = json_encode(['gateway' => 'dummy_stripe', 'tx_id' => uniqid('tx_'), 'amount' => $total]);
    $upd_stmt = $pdo->prepare("UPDATE orders SET status = 'paid', payment_info = ? WHERE id = ?");
    $upd_stmt->execute([$payment_info, $order_id]);
    echo "<script>alert('Payment successful!'); location.reload();</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 2rem; }
        .section { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; }
        .card { padding: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        .status { padding: 0.25rem 0.5rem; border-radius: 3px; color: white; }
        .paid { background: #28a745; }
        .pending { background: #ffc107; color: black; }
        button { background: #ff6600; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="section">
            <h2><?php echo ucfirst($role); ?> Orders</h2>
            <div class="grid">
                <?php foreach ($orders as $order): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($order['product_name']); ?></h3>
                        <p>Quantity: <?php echo $order['quantity']; ?> | Total: $<?php echo $order['total_price']; ?></p>
                        <span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                        <?php if ($role === 'buyer' && $order['status'] === 'pending'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <input type="hidden" name="total" value="<?php echo $order['total_price']; ?>">
                                <button name="pay_order" type="submit">Pay Now (Dummy)</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="#" onclick="window.location.href='dashboard.php'" style="display: block; text-align: center;">Back to Dashboard</a>
    </div>
</body>
</html>
