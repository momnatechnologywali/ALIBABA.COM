<?php
session_start();
require_once 'db.php';
 
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$product) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; }
        .container { max-width: 800px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .product-img { width: 100%; max-width: 400px; height: auto; margin-bottom: 1rem; }
        h1 { color: #0066cc; margin-bottom: 1rem; }
        .price { color: #ff6600; font-size: 1.5rem; font-weight: bold; margin: 1rem 0; }
        button { background: #ff6600; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; margin-right: 1rem; transition: background 0.3s; }
        button:hover { background: #e55a00; }
        .contact { background: #0066cc; }
        .contact:hover { background: #004499; }
        @media (max-width: 768px) { .container { margin: 1rem; padding: 1rem; } }
    </style>
</head>
<body>
    <div class="container">
        <img src="<?php echo json_decode($product['images'])[0] ?? 'https://via.placeholder.com/400'; ?>" alt="" class="product-img">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <div class="price">$<?php echo $product['price']; ?> | MOQ: <?php echo $product['moq']; ?> | Category: <?php echo htmlspecialchars($product['category']); ?></div>
        <p>Seller: <?php echo htmlspecialchars($product['company_name']); ?></p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'buyer'): ?>
            <button onclick="requestQuote(<?php echo $id; ?>)">Request Quotation</button>
            <button class="contact" onclick="sendMessage(<?php echo $product['seller_id']; ?>)">Contact Supplier</button>
        <?php endif; ?>
        <a href="#" onclick="window.location.href='search.php'" style="display: block; margin-top: 1rem;">Back to Search</a>
    </div>
    <script>
        function requestQuote(id) {
            const qty = prompt('Enter quantity for quote:');
            if (qty) window.location.href = `request_quotation.php?id=${id}&qty=${qty}`;
        }
        function sendMessage(toId) {
            window.location.href = `messages.php?to=${toId}`;
        }
    </script>
</body>
</html>
