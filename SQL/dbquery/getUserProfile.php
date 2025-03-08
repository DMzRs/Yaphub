<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins (use a specific one in production)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
require_once __DIR__ . "/../config/DBConnection.php"; // Ensure correct path

if (!isset($_GET['user_id'])) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit();
}

$user_id = $_GET['user_id'];

try {
    $query = "SELECT firstName, lastName, profile_picture FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(["success" => true, "profile" => $user]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
