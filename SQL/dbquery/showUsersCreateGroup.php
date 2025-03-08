<?php
session_start();
require '../config/DBConnection.php';

header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$currentUserId = $_SESSION['currentUserId'];

try {
    // Query to fetch friends
    $sql = "
        SELECT DISTINCT u.user_id AS id, u.firstName AS first_name, u.lastName AS last_name, u.profile_picture AS profile_img 
        FROM users u
        JOIN friends f 
        ON (f.user_id1 = :currentUserId AND f.user_id2 = u.user_id) 
        OR (f.user_id2 = :currentUserId AND f.user_id1 = u.user_id)
        WHERE f.status = 'accepted'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['currentUserId' => $currentUserId]);
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitize output
    $friends = array_map(function ($friend) {
        return [
            "id" => $friend['id'],
            "first_name" => htmlspecialchars($friend['first_name']),
            "last_name" => htmlspecialchars($friend['last_name']),
            "profile_img" => htmlspecialchars($friend['profile_img'] ?? 'images/profile_img/default_profile.jpg')
        ];
    }, $friends);

    echo json_encode(["success" => true, "friends" => $friends]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error: " . $e->getMessage()]);
}
exit;
