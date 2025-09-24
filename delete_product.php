<?php
session_start();
require_once 'db.php';
 
if ($_POST['id'] && $_SESSION['role'] === 'seller') {
    $id = intval($_POST['id']);
    $seller_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$id, $seller_id]);
}
echo json_encode(['status' => 'deleted']);
?>
