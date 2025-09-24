<?php
session_start();
require_once 'db.php';
 
$error = '';
$success = '';
 
if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $company = trim($_POST['company']);
 
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists.';
        } else {
            $hashed = hashPassword($password);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, company_name) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed, $role, $company])) {
                $success = 'Account created! Redirecting...';
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['role'] = $role;
                echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);</script>";
            } else {
                $error = 'Signup failed.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1rem; color: #0066cc; }
        input, select { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 0.75rem; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #004499; }
        .error { color: red; text-align: center; margin-bottom: 1rem; }
        .success { color: green; text-align: center; margin-bottom: 1rem; }
        @media (max-width: 480px) { .form-container { margin: 1rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="buyer">Buyer</option>
                <option value="seller">Seller</option>
            </select>
            <input type="text" name="company" placeholder="Company Name">
            <button type="submit">Sign Up</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="#" onclick="window.location.href='login.php'">Already have an account? Login</a></p>
    </div>
    <script>
        // Auto-focus first input
        document.querySelector('input').focus();
    </script>
</body>
</html>
