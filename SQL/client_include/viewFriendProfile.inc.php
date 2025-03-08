<?php
require(__DIR__ . "/../config/DBConnection.php");

session_start();

if (!isset($_SESSION['currentUserId'])) {
    header("Location: index.php");
    exit;
}

$current_user_id = $_SESSION['currentUserId'];
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    $query = $pdo->prepare("
        SELECT user_id, firstName, lastName, profile_picture, email, is_online, address 
        FROM users 
        WHERE user_id = :user_id
    ");
    $query->execute(['user_id' => $user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Ensure the profile_picture is not null; otherwise, set a default.
    if (empty($user['profile_picture'])) {
        $user['profile_picture'] = 'images/profile_img/default_profile.jpg';  // Fallback if no image is set
    }

    // To check friendship status
    $friendshipQuery = $pdo->prepare("
        SELECT status 
        FROM friends 
        WHERE (user_id1 = :current_user AND user_id2 = :profile_user)
           OR (user_id1 = :profile_user AND user_id2 = :current_user)
    ");
    $friendshipQuery->execute([
        'current_user' => $current_user_id,
        'profile_user' => $user_id
    ]);

    $friendship = $friendshipQuery->fetch(PDO::FETCH_ASSOC);
    $friendshipStatus = $friendship['status'] ?? null; // 'accepted', 'pending', or null

    //friend count query
    $query = "SELECT COUNT(*) AS friend_count FROM friends WHERE (user_id1 = :userId OR user_id2 = :userId) AND status = 'accepted' ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $friendCount = $stmt->fetch(PDO::FETCH_ASSOC)['friend_count'];

    //group count query(excluded the chat_id with is_group value 0)
    $query = "SELECT COUNT(*) AS group_count 
            FROM chat_members cm
            JOIN chats c ON cm.chat_id = c.chat_id
            WHERE cm.user_id = :userId AND c.is_group = 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $groupCount = $stmt->fetch(PDO::FETCH_ASSOC)['group_count'];

    // Query to get the chat_id for a private chat between the current user and the profile user
    $chatQuery = $pdo->prepare("
    SELECT chat_id 
    FROM chats 
    WHERE is_group = 0 
    AND chat_id IN (
        SELECT chat_id 
        FROM chat_members 
        WHERE user_id = :current_user
    ) 
    AND chat_id IN (
        SELECT chat_id 
        FROM chat_members 
        WHERE user_id = :profile_user
    )
    ");
    $chatQuery->execute([
    'current_user' => $current_user_id,
    'profile_user' => $user_id
    ]);

    $chat = $chatQuery->fetch(PDO::FETCH_ASSOC);
    $chat_id = $chat['chat_id'] ?? null; // Set to null if no chat exists
} else {
    echo "No user specified!";
    exit;
}


