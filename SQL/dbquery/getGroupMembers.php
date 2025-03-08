<?php
require __DIR__ . '/../config/DBConnection.php';

header("Content-Type: application/json"); // Ensure JSON response

if (!isset($_GET['group_id'])) {
    echo json_encode(["error" => "Missing group_id"]);
    exit;
}

$groupId = intval($_GET['group_id']);

try {
    $stmt = $pdo->prepare("SELECT users.user_id, CONCAT(users.firstName, ' ', users.lastName) AS username 
                           FROM users 
                           JOIN chat_members ON users.user_id = chat_members.user_id 
                           WHERE chat_members.chat_id = ?");
    $stmt->execute([$groupId]);

    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($members);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
