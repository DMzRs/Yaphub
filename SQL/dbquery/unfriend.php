<?php
require(__DIR__ . "/../config/DBConnection.php");
session_start();

if (!isset($_SESSION['currentUserId'])) {
    header("Location: ../../client/loginPage.php");
    exit();
}

$current_user_id = $_SESSION['currentUserId'];

if (isset($_POST['friend_id'])) {
    $friend_id = $_POST['friend_id'];

    try {
        $deleteQuery = $pdo->prepare("
            DELETE FROM friends
            WHERE (user_id1 = :current_user_id AND user_id2 = :friend_id)
               OR (user_id1 = :friend_id AND user_id2 = :current_user_id)
        ");
        $deleteQuery->execute([
            'current_user_id' => $current_user_id,
            'friend_id' => $friend_id
        ]);

        // Optionally, redirect back to the profile page or friends list
        header("Location: ../../client/discoverPeoplePage.php");
        exit();
    } catch (PDOException $e) {
        // Handle errors if needed
        header("Location: ../../client/discoverPeoplePage.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // If no friend_id is provided, redirect
    header("Location: ../../client/discoverPeoplePage.php");
    exit();
}
