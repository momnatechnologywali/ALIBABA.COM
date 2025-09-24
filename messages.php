<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require_once 'db.php';
 
$user_id = $_SESSION['user_id'];
$to_id = intval($_GET['to'] ?? 0);
 
if ($_POST && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (from_id, to_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $to_id ?: 0, $message]); // If no to_id, it's general
        echo "<script>location.reload();</script>";
    }
}
 
// Fetch messages
$sql = "SELECT m.*, u.username as from_name FROM messages m JOIN users u ON m.from_id = u.id WHERE (m.from_id = ? AND m.to_id = ?) OR (m.from_id = ? AND m.to_id = ?) ORDER BY created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $to_id, $to_id, $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Fetch conversations
$stmt = $pdo->prepare("SELECT DISTINCT u.id, u.username, u.company_name FROM messages m JOIN users u ON m.from_id = u.id WHERE m.to_id = ? OR m.from_id = ?");
$stmt->execute([$user_id, $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Alibaba Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; display: flex; height: 100vh; }
        .sidebar { width: 250px; background: white; overflow-y: auto; border-right: 1px solid #ddd; }
        .chat { flex: 1; display: flex; flex-direction: column; }
        .messages { flex: 1; padding: 1rem; overflow-y: auto; }
        .message { margin-bottom: 1rem; padding: 0.5rem; border-radius: 10px; max-width: 70%; }
        .sent { background: #0066cc; color: white; margin-left: auto; }
        .received { background: #e9ecef; }
        .input-area { padding: 1rem; background: white; border-top: 1px solid #ddd; }
        textarea { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; resize: none; }
        button { background: #ff6600; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; }
        .conv-item { padding: 1rem; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.3s; }
        .conv-item:hover { background: #f8f9fa; }
        .conv-item.active { background: #e3f2fd; }
        @media (max-width: 768px) { body { flex-direction: column; } .sidebar { width: 100%; height: auto; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Conversations</h3>
        <?php foreach ($conversations as $conv): ?>
            <div class="conv-item <?php echo $conv['id'] == $to_id ? 'active' : ''; ?>" onclick="openChat(<?php echo $conv['id']; ?>)">
                <?php echo htmlspecialchars($conv['company_name'] ?? $conv['username']); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="chat">
        <div class="messages" id="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo $msg['from_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <strong><?php echo htmlspecialchars($msg['from_name']); ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?>
                    <br><small><?php echo $msg['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="input-area">
            <form method="POST" onsubmit="sendMessage(); return false;">
                <textarea name="message" id="msgInput" placeholder="Type a message..." rows="2" required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    <script>
        let currentTo = <?php echo $to_id; ?>;
        function openChat(toId) {
            currentTo = toId;
            window.location.href = `messages.php?to=${toId}`;
        }
        function sendMessage() {
            const msg = document.getElementById('msgInput').value;
            if (msg && currentTo) {
                fetch('send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `to_id=${currentTo}&message=${encodeURIComponent(msg)}`
                }).then(() => {
                    document.getElementById('msgInput').value = '';
                    location.reload();
                });
            }
        }
    </script>
</body>
</html>
