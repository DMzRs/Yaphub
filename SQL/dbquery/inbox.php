<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require(__DIR__ . "/../config/DBConnection.php");

if (!isset($_SESSION['currentUserId'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['currentUserId'];

try {
    $sql = "
    SELECT 
        u.user_id, 
        u.firstName, 
        u.lastName, 
        u.profile_picture, 
        c.chat_id,
        COALESCE(NULLIF(m.message_text, ''), 'No messages yet') AS latest_message,
        COALESCE(m.message_type, '') AS latest_file_type,
        m.created_at AS message_timestamp
    FROM friends f
    JOIN users u ON (u.user_id = f.user_id1 OR u.user_id = f.user_id2)
    JOIN chat_members cm1 ON cm1.user_id = :userId
    JOIN chat_members cm2 ON cm2.user_id = u.user_id AND cm1.chat_id = cm2.chat_id
    JOIN chats c ON c.chat_id = cm1.chat_id
    LEFT JOIN messages m ON m.chat_id = c.chat_id 
        AND m.created_at = (
            SELECT MAX(created_at) 
            FROM messages 
            WHERE chat_id = c.chat_id
        )
    WHERE (f.user_id1 = :userId OR f.user_id2 = :userId)
        AND u.user_id != :userId
        AND f.status = 'accepted'
        AND c.is_group = 0
    GROUP BY u.user_id, u.firstName, u.lastName, u.profile_picture, c.chat_id, m.message_text, m.message_type, m.created_at
    ORDER BY m.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $inbox = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Modify output to show 'sent a photo' or 'sent a video' if message_text is null
    foreach ($inbox as &$chat) {
        if ($chat['latest_message'] === null && $chat['latest_file_type']) {
            if (strpos($chat['latest_file_type'], 'image') !== false) {
                $chat['latest_message'] = "Sent a photo";
            } elseif (strpos($chat['latest_file_type'], 'video') !== false) {
                $chat['latest_message'] = "Sent a video";
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($inbox);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
