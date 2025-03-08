<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require(__DIR__ . "/../config/DBConnection.php");

$userId = $_SESSION['currentUserId'];

try {
    $sql = "
    SELECT c.chat_id, c.chat_name, c.group_picture, 
        COALESCE(NULLIF(m.message_text, ''), 'No messages yet') AS latest_message, 
        m.message_type AS latest_file_type,
        m.created_at AS message_timestamp
    FROM chat_members cm
    JOIN chats c ON cm.chat_id = c.chat_id
    LEFT JOIN (
        SELECT m1.chat_id, m1.message_text, m1.message_type, m1.created_at
        FROM messages m1
        WHERE m1.message_id = (
            SELECT m2.message_id 
            FROM messages m2 
            WHERE m2.chat_id = m1.chat_id 
            ORDER BY m2.created_at DESC 
            LIMIT 1
        )
    ) m ON m.chat_id = c.chat_id
    WHERE cm.user_id = :userId 
    AND c.is_group = 1
    ORDER BY m.created_at DESC, c.chat_name ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($groups);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
