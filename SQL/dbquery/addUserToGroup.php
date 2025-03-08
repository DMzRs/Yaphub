<?php
require __DIR__ . '/../config/DBConnection.php';

$groupId = $_POST['group_id'];
$userId = $_POST['user_id'];

$stmt = $pdo->prepare("INSERT INTO chat_members (chat_id, user_id) VALUES (?, ?)");
$stmt->execute([$groupId, $userId]);

echo json_encode(["status" => "success"]);

