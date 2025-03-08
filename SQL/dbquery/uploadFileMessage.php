<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins (use a specific one in production)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Include database connection
require_once __DIR__ . "/../config/DBConnection.php";

// Set upload directory
$uploadDir = __DIR__ . "/../../client/fileUpload/"; // Adjust according to your project structure

// Ensure the directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check for required fields
if (!isset($_FILES['file']) || !isset($_POST['user_id']) || !isset($_POST['chat_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$file = $_FILES['file'];
$fileName = basename($file['name']);
$fileTmpPath = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$user_id = $_POST['user_id'];
$chat_id = $_POST['chat_id'];

// Validate user_id and chat_id
if (!is_numeric($user_id) || !is_numeric($chat_id)) {
    echo json_encode(["success" => false, "message" => "Invalid user or chat ID."]);
    exit;
}

// Allowed file types
$allowedExtensions = ['jpg', 'jpeg', 'png', 'mp4'];

if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(["success" => false, "message" => "Invalid file type."]);
    exit;
}

// Generate a unique filename to prevent overwriting
$newFileName = uniqid("file_", true) . "." . $fileExt;
$fileDestination = $uploadDir . $newFileName;

// Move the file to the upload directory
if (move_uploaded_file($fileTmpPath, $fileDestination)) {
    // Get the relative path for frontend use
    $fileURL = "fileUpload/" . $newFileName;

    echo json_encode(["success" => true, "file_url" => $fileURL, "file_type" => ($fileExt === 'mp4' ? 'video' : 'image')]);
} else {
    echo json_encode(["success" => false, "message" => "File upload failed."]);
}
?>
