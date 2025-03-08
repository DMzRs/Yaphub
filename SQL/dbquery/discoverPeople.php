<?php
session_start();
require(__DIR__ . "/../config/DBConnection.php");
$current_user_id = $_SESSION['currentUserId'];

// fetch non-friend account regardless if online or dili
$queryNonFriends = $pdo->prepare("
    SELECT u.user_id, u.firstName, u.lastName, u.profile_picture,
           (SELECT COUNT(*) 
            FROM friends f 
            WHERE ((f.user_id1 = :current_user_id AND f.user_id2 = u.user_id) 
               OR (f.user_id1 = u.user_id AND f.user_id2 = :current_user_id))
              AND f.status = 'pending') AS is_pending
    FROM users u
    WHERE u.user_id != :current_user_id
    AND u.user_id NOT IN (
        SELECT CASE 
                WHEN f.user_id1 = :current_user_id THEN f.user_id2 
                ELSE f.user_id1 
               END 
        FROM friends f 
        WHERE (f.user_id1 = :current_user_id OR f.user_id2 = :current_user_id) 
        AND f.status = 'accepted'
    )
");

$queryNonFriends->execute(['current_user_id' => $current_user_id]);
$nonFriends = $queryNonFriends->fetchAll(PDO::FETCH_ASSOC);


//fetch friends regardless and tell if online or dili
$queryFriends = $pdo->prepare("
    SELECT DISTINCT u.user_id, u.firstName, u.lastName, u.profile_picture, u.is_online
    FROM users u
    JOIN friends f ON (u.user_id = f.user_id1 OR u.user_id = f.user_id2)
    WHERE f.status = 'accepted'
    AND (
        (f.user_id1 = :current_user_id AND u.user_id = f.user_id2)
        OR (f.user_id2 = :current_user_id AND u.user_id = f.user_id1)
    )
");
$queryFriends->execute(['current_user_id' => $current_user_id]);
$friends = $queryFriends->fetchAll(PDO::FETCH_ASSOC);



//fetch friend request na mark ug pending sa table
$queryFriendRequests = $pdo->prepare("
    SELECT u.user_id, u.firstName, u.lastName, u.profile_picture 
    FROM friends f
    JOIN users u ON u.user_id = f.user_id1
    WHERE f.user_id2 = :current_user_id
    AND f.status = 'pending'
");
$queryFriendRequests->execute(['current_user_id' => $current_user_id]);
$friendRequests = $queryFriendRequests->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'friends' => $friends,
    'nonFriends' => $nonFriends,
    'friendRequests' => $friendRequests
]);
exit;