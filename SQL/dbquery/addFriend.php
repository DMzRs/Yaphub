<?php
require(__DIR__ . "/../config/DBConnection.php");
session_start();

$current_user_id = $_SESSION['currentUserId'];

if (isset($_POST['add_friend_id'])) {
    $friend_id = $_POST['add_friend_id'];

    try {
        // Check if a friend request already exists
        $checkQuery = $pdo->prepare("
            SELECT * FROM friends 
            WHERE (user_id1 = :current_user_id AND user_id2 = :friend_id) 
               OR (user_id1 = :friend_id AND user_id2 = :current_user_id)
        ");
        $checkQuery->execute([
            'current_user_id' => $current_user_id,
            'friend_id' => $friend_id
        ]);

        $existingRequest = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if (!$existingRequest) {
            // Insert the friend request
            $insertQuery = $pdo->prepare("
                INSERT INTO friends (user_id1, user_id2, status) 
                VALUES (:current_user_id, :friend_id, 'pending')
            ");
            $insertQuery->execute([
                'current_user_id' => $current_user_id,
                'friend_id' => $friend_id
            ]);
        
            // Check if a private chat already exists between these users
            $checkChatQuery = $pdo->prepare("
                SELECT c.chat_id FROM chats c
                JOIN chat_members cm1 ON c.chat_id = cm1.chat_id
                JOIN chat_members cm2 ON c.chat_id = cm2.chat_id
                WHERE c.is_group = 0
                AND cm1.user_id = :user1 
                AND cm2.user_id = :user2
            ");
            $checkChatQuery->execute([
                'user1' => $current_user_id,
                'user2' => $friend_id
            ]);
            $existingChat = $checkChatQuery->fetch(PDO::FETCH_ASSOC);
        
            if (!$existingChat) {
                // Create a private chat for the two users
                $chatQuery = $pdo->prepare("
                    INSERT INTO chats (chat_name, is_group) 
                    VALUES (NULL, 0)
                ");
                $chatQuery->execute();
                $chatId = $pdo->lastInsertId();
        
                // Add both users to chat_members
                $chatMemberQuery = $pdo->prepare("
                    INSERT INTO chat_members (chat_id, user_id) 
                    VALUES (:chat_id, :user1), (:chat_id, :user2)
                ");
                $chatMemberQuery->execute([
                    'chat_id' => $chatId,
                    'user1' => $current_user_id,
                    'user2' => $friend_id
                ]);
            }
        } else {
            // Add a pop-up or message for pending friend requests
        }
        
        header("Location: ../../client/discoverPeoplePage.php");
        exit();        
    } catch (PDOException $e) {
        // error handling onle
        header("Location: ../../client/discoverPeoplePage.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    //Return to discover people page
    http_response_code(200);
    exit();
}
