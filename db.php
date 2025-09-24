<?php
// Database configuration
$host = 'localhost'; // Assuming standard MySQL host
$dbname = 'dbohlmdnndwccg';
$username = 'uhpdlnsnj1voi';
$password = 'rowrmxvbu3z5';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// Function to hash password (use in signup)
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
 
// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>
