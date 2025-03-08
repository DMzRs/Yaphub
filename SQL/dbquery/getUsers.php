<?php
require __DIR__ . '/../config/DBConnection.php';

header("Content-Type: application/json"); // Ensure JSON response

session_start();
if (!isset($_SESSION['currentUserId'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

if (!isset($_GET['group_id'])) {
    echo json_encode(["error" => "Missing group_id"]);
    exit;
}

$currentUserId = intval($_SESSION['currentUserId']);
$groupId = intval($_GET['group_id']);

try {
    $stmt = $pdo->prepare("
        SELECT u.user_id, CONCAT(u.firstName, ' ', u.lastName) AS username
        FROM users u
        JOIN friends f ON (
            (f.user_id1 = :currentUserId AND f.user_id2 = u.user_id) 
            OR (f.user_id2 = :currentUserId AND f.user_id1 = u.user_id)
        )
        WHERE f.status = 'accepted'
        AND u.user_id NOT IN (
            SELECT user_id FROM chat_members WHERE chat_id = :groupId
        )
    ");
    
    $stmt->execute([
        ':currentUserId' => $currentUserId,
        ':groupId' => $groupId
    ]);
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

