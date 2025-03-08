<?php
session_start();
require '../config/DBConnection.php';

header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = json_decode(file_get_contents("php://input"), true);

$userId = $_SESSION['currentUserId'];
$groupName = $input['groupName'] ?? null;
$members = $input['members'] ?? [];

if (!$groupName || empty($members)) {
    echo json_encode(["success" => false, "error" => "Invalid group data"]);
    exit;
}

try {
    // Insert group into chats table
    $stmt = $pdo->prepare("INSERT INTO chats (chat_name, is_group) VALUES (?, 1)");
    $stmt->execute([$groupName]);
    $chatId = $pdo->lastInsertId();

    // Add group members (including creator)
    $members[] = $userId;
    $stmt = $pdo->prepare("INSERT INTO chat_members (chat_id, user_id) VALUES (?, ?)");
    
    foreach ($members as $member) {
        $stmt->execute([$chatId, $member]);
    }

    echo json_encode(["success" => true, "chat_id" => $chatId]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error: " . $e->getMessage()]);
}
?>
