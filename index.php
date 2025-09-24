<?php
session_start();
require_once 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    // Guest view
    $stmt = $pdo->query("SELECT * FROM products WHERE availability = 1 LIMIT 6");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'seller' LIMIT 4");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Logged-in view (personalized)
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->query("SELECT * FROM products WHERE availability = 1 LIMIT 6");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'seller' LIMIT 4");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alibaba Clone - Wholesale Marketplace</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; color: #333; }
        header { background: linear-gradient(135deg, #0066cc, #004499); color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        nav ul { list-style: none; display: flex; gap: 1rem; }
        nav a { color: white; text-decoration: none; padding: 0.5rem; border-radius: 5px; transition: background 0.3s; }
        nav a:hover { background: rgba(255,255,255,0.2); }
        .login-btn { background: #ff6600; padding: 0.5rem 1rem; border-radius: 5px; }
        .hero { background: #0066cc; color: white; text-align: center; padding: 4rem 2rem; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; }
        .search-bar { max-width: 600px; margin: 2rem auto; display: flex; }
        .search-bar input { flex: 1; padding: 1rem; border: none; border-radius: 5px 0 0 5px; }
        .search-bar button { padding: 1rem 2rem; background: #ff6600; color: white; border: none; border-radius: 0 5px 5px 0; cursor: pointer; transition: background 0.3s; }
        .search-bar button:hover { background: #e55a00; }
        .section { padding: 3rem 2rem; }
        .section h2 { text-align: center; margin-bottom: 2rem; color: #0066cc; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 1rem; }
        .card h3 { margin-bottom: 0.5rem; }
        .price { color: #ff6600; font-weight: bold; font-size: 1.2rem; }
        .moq { color: #666; font-size: 0.9rem; }
        footer { background: #333; color: white; text-align: center; padding: 2rem; }
        @media (max-width: 768px) { .hero h1 { font-size: 2rem; } .search-bar { flex-direction: column; } }
    </style>
</head>
<body>
    <header>
        <h2>Alibaba Clone</h2>
        <nav>
            <ul>
                <li><a href="#" onclick="window.location.href='index.php'">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="#" onclick="window.location.href='dashboard.php'">Dashboard</a></li>
                    <li><a href="#" onclick="logout()">Logout</a></li>
                <?php else: ?>
                    <li><a href="#" onclick="window.location.href='login.php'">Login</a></li>
                    <li><a href="#" onclick="window.location.href='signup.php'">Signup</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <section class="hero">
        <h1>Global Wholesale Trade Starts Here</h1>
        <p>Connect with suppliers and buyers worldwide.</p>
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search products, suppliers...">
            <button onclick="searchProducts()">Search</button>
        </div>
    </section>
    <section class="section">
        <h2>Trending Products</h2>
        <div class="grid">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <img src="<?php echo json_decode($product['images'])[0] ?? 'https://via.placeholder.com/250x200?text=No+Image'; ?>" alt="<?php echo $product['name']; ?>">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo substr($product['description'], 0, 100); ?>...</p>
                        <div class="price">$<?php echo $product['price']; ?></div>
                        <div class="moq">MOQ: <?php echo $product['moq']; ?></div>
                        <button onclick="viewProduct(<?php echo $product['id']; ?>)">View Details</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="section">
        <h2>Featured Suppliers</h2>
        <div class="grid">
            <?php foreach ($suppliers as $supplier): ?>
                <div class="card">
                    <img src="https://via.placeholder.com/250x200?text=<?php echo $supplier['company_name']; ?>" alt="<?php echo $supplier['company_name']; ?>">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($supplier['company_name']); ?></h3>
                        <p>Rating: <?php echo $supplier['rating']; ?>/5</p>
                        <button onclick="viewSupplier(<?php echo $supplier['id']; ?>)">Contact</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <footer>
        <p>&copy; 2025 Alibaba Clone. All rights reserved.</p>
    </footer>
    <script>
        function searchProducts() {
            const query = document.getElementById('search').value;
            window.location.href = `search.php?q=${encodeURIComponent(query)}`;
        }
        function viewProduct(id) {
            window.location.href = `product_detail.php?id=${id}`;
        }
        function viewSupplier(id) {
            window.location.href = `supplier_profile.php?id=${id}`;
        }
        function logout() {
            fetch('logout.php', { method: 'POST' }).then(() => {
                window.location.href = 'index.php';
            });
        }
        // Enter key for search
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchProducts();
        });
    </script>
</body>
</html>
