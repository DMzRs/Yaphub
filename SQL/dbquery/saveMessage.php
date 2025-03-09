<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins (use a specific one in production)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
require_once __DIR__ . "/../config/DBConnection.php";

// Get request data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'], $data['chat_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit();
}

$user_id = $data['user_id'];
$chat_id = $data['chat_id'];
$text = isset($data['text']) ? $data['text'] : null;
$file_url = isset($data['file_url']) ? $data['file_url'] : null;
$file_type = isset($data['file_type']) ? $data['file_type'] : null;

// Determine message type
$message_type = "text"; // Default is text message

if ($file_url) {
    $message_type = $file_type === "video" ? "video" : "image";
}

// Insert message into the database
$query = "INSERT INTO messages (chat_id, sender_id, message_text, message_type, file_url, created_at) 
          VALUES (:chat_id, :user_id, :message_text, :message_type, :file_url, NOW())";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':message_text', $text, PDO::PARAM_STR);
$stmt->bindParam(':message_type', $message_type, PDO::PARAM_STR);
$stmt->bindParam(':file_url', $file_url, PDO::PARAM_STR);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to store message"]);
}
?>
