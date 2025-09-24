<?php
session_start();
require_once 'db.php';
 
if (!isset($_SESSION['user_id']) || !isset($_POST['to_id']) || !isset($_POST['message'])) {
    echo json_encode(['status' => 'error']);
    exit;
}
 
$user_id = $_SESSION['user_id'];
$to_id = intval($_POST['to_id']);
$message = trim($_POST['message']);
 
$stmt = $pdo->prepare("INSERT INTO messages (from_id, to_id, message) VALUES (?, ?, ?)");
if ($stmt->execute([$user_id, $to_id, $message])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>
