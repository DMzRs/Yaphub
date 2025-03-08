<?php
require(__DIR__ . "/../config/DBConnection.php");

session_start();

if (!isset($_SESSION['currentUserId'])) {
    header("Location: index.php");
    exit;
}

$current_user_id = $_SESSION['currentUserId']; //get the logged-in user ID

//friend count query
$query = "SELECT COUNT(*) AS friend_count FROM friends WHERE (user_id1 = :userId OR user_id2 = :userId) AND status = 'accepted' ";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':userId', $current_user_id, PDO::PARAM_INT);
$stmt->execute();
$friendCount = $stmt->fetch(PDO::FETCH_ASSOC)['friend_count'];

//group count query(excluded the chat_id with is_group value 0)
$query = "SELECT COUNT(*) AS group_count 
          FROM chat_members cm
          JOIN chats c ON cm.chat_id = c.chat_id
          WHERE cm.user_id = :userId AND c.is_group = 1";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':userId', $current_user_id, PDO::PARAM_INT);
$stmt->execute();
$groupCount = $stmt->fetch(PDO::FETCH_ASSOC)['group_count'];

