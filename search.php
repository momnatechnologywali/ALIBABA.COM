<?php
session_start();
require_once 'db.php';
 
$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 99999;
$min_moq = $_GET['min_moq'] ?? 0;
 
$sql = "SELECT * FROM products WHERE availability = 1 AND name LIKE ? OR description LIKE ?";
$params = ["%$query%", "%$query%"];
if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
$sql .= " AND price BETWEEN ? AND ? AND moq >= ?";
$params[] = $min_price; $params[] = $max_price; $params[] = $min_moq;
 
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; }
        header { background: #0066cc; color: white; padding: 1rem; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .filters { background: white; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap; }
        .filters input, .filters select { padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .filters button { background: #ff6600; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 1rem; }
        .price { color: #ff6600; font-weight: bold; }
        @media (max-width: 768px) { .filters { flex-direction: column; } }
    </style>
</head>
<body>
    <header>
        <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
        <a href="#" onclick="window.location.href='index.php'" style="color: white;">Home</a>
    </header>
    <div class="container">
        <form method="GET" class="filters">
            <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search...">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Apparel">Apparel</option>
                <option value="Electronics">Electronics</option>
                <!-- Add more -->
            </select>
            <input type="number" name="min_price" value="<?php echo $min_price; ?>" placeholder="Min Price">
            <input type="number" name="max_price" value="<?php echo $max_price; ?>" placeholder="Max Price">
            <input type="number" name="min_moq" value="<?php echo $min_moq; ?>" placeholder="Min MOQ">
            <button type="submit">Filter</button>
        </form>
        <div class="grid">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <img src="<?php echo json_decode($product['images'])[0] ?? 'https://via.placeholder.com/250x200'; ?>" alt="">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo substr($product['description'], 0, 100); ?>...</p>
                        <div class="price">$<?php echo $product['price']; ?> | MOQ: <?php echo $product['moq']; ?></div>
                        <button onclick="requestQuote(<?php echo $product['id']; ?>)">Request Quote</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function requestQuote(id) {
            const qty = prompt('Enter quantity:');
            if (qty) window.location.href = `request_quotation.php?id=${id}&qty=${qty}`;
        }
    </script>
</body>
</html>
