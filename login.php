<?php
session_start();
require_once 'db.php';
 
$error = '';
 
if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 1000);</script>";
    } else {
        $error = 'Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1rem; color: #0066cc; }
        input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 0.75rem; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #004499; }
        .error { color: red; text-align: center; margin-bottom: 1rem; }
        @media (max-width: 480px) { .form-container { margin: 1rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="#" onclick="window.location.href='signup.php'">New user? Sign Up</a></p>
    </div>
    <script>
        document.querySelector('input').focus();
    </script>
</body>
</html>
