<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once __DIR__ . "/../config/DBConnection.php";
session_start();

if (!isset($_SESSION['currentUserId'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$current_user_id = $_SESSION['currentUserId'];

if (!isset($_GET['chat_id'])) {
    echo json_encode(["success" => false, "message" => "Chat ID is required"]);
    exit;
}

$chat_id = $_GET['chat_id'];

try {
    // Get chat details, but exclude current_user if it's a private chat
    $query = "SELECT c.chat_id, c.chat_name, c.group_picture, c.is_group, u.user_id, u.profile_picture, u.firstName, u.lastName
              FROM chats c
              JOIN chat_members cm ON c.chat_id = cm.chat_id
              JOIN users u ON cm.user_id = u.user_id
              WHERE c.chat_id = :chat_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":chat_id", $chat_id, PDO::PARAM_INT);
    $stmt->execute();
    $chatDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$chatDetails) {
        echo json_encode(["success" => false, "message" => "Chat not found"]);
        exit;
    }

    $is_group = (bool) $chatDetails[0]['is_group'];

    if ($is_group) {
        // For group chats, use chat name and group picture
        $chatName = $chatDetails[0]['chat_name'];
        $profileImg = $chatDetails[0]['group_picture'] ?? 'images/group_img/default_group.jpg';

        $groupMembers = [];
        foreach ($chatDetails as $member) {
            $groupMembers[] = $member['firstName'] . " " . $member['lastName'];
        }
        $user_id = null; // No user_id needed for groups
    } else {
        // For private chats, exclude current user and fetch only the other user
        foreach ($chatDetails as $user) {
            if ($user['user_id'] != $current_user_id) {
                $chatName = $user['firstName'] . " " . $user['lastName'];
                $profileImg = $user['profile_picture'] ?? 'images/profile_img/default_profile.jpg';
                $user_id = $user['user_id']; // Set the user_id of the other user
                break; // Stop after finding the other user
            }
        }
        $groupMembers = null; // Not applicable for private chats
    }

    echo json_encode([
        "success" => true,
        "chat_id" => $chat_id,
        "chat_name" => $chatName,
        "profile_picture" => $profileImg,
        "is_group" => $is_group,
        "group_members" => $groupMembers,
        "user_id" => $user_id // Send user_id for private chats only
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
