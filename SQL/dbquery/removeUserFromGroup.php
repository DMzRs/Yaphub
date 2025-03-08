<?php
require __DIR__ . '/../config/DBConnection.php';

header("Content-Type: application/json");

// Validate input
if (!isset($_POST['group_id'], $_POST['user_id'])) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$groupId = $_POST['group_id'];
$userId = $_POST['user_id'];

try {
    // Check if the chat is a group
    $stmt = $pdo->prepare("SELECT is_group FROM chats WHERE chat_id = ?");
    $stmt->execute([$groupId]);
    $chat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chat || $chat['is_group'] != 1) {
        echo json_encode(["error" => "Invalid group chat"]);
        exit;
    }

    // Delete the user from the group chat
    $stmt = $pdo->prepare("DELETE FROM chat_members WHERE chat_id = ? AND user_id = ?");
    $stmt->execute([$groupId, $userId]);

    // Use "success" instead of "status"
    echo json_encode(["success" => true]);
    exit;
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
