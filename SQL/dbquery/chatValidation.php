<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins (use a specific one in production)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbPath = __DIR__ . "/../config/DBConnection.php";
if (!file_exists($dbPath)) {
    echo json_encode(["success" => false, "message" => "Database connection file not found"]);
    exit();
}
require_once $dbPath;

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'], $data['chat_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit();
}

$user_id = $data['user_id'];
$chat_id = $data['chat_id'];

$query = "SELECT COUNT(*) AS count FROM chat_members WHERE user_id = :user_id AND chat_id = :chat_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => $result['count'] > 0,
    "message" => $result['count'] > 0 ? "User is in the chat" : "User is not a member of this chat"
]);
?>
