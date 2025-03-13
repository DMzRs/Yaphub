<?php
session_start();
require(__DIR__ . "/../config/DBConnection.php");

header("Content-Type: application/json");

$userId = $_SESSION['currentUserId'] ?? null;

$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';

$profilePicture = $_FILES['profile_picture'] ?? null;

try {
    $pdo->beginTransaction();

    // Get current user data
    $stmt = $pdo->prepare("SELECT password, profile_picture FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found!');
    }

    // Verify current password if provided
    if (!empty($currentPassword)) {
        if (strlen($currentPassword) < 8) {
            throw new Exception('Current password must be at least 8 characters long!');
        }
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception('Current password is incorrect!');
        }

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 8) {
                throw new Exception('New password must be at least 8 characters long!');
            }
            if ($newPassword === $currentPassword) {
                throw new Exception('New password must be different from the current password!');
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            throw new Exception('New password cannot be empty if changing password!');
        }
    } else {
        $hashedPassword = $user['password'];
    }

    // Handle profile picture upload if provided
    $profilePicturePath = $user['profile_picture']; // Keep existing picture
    if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($profilePicture['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Only JPG and PNG files are allowed!');
        }

        $targetDir = __DIR__ . "/../../client/images/profile_img/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // **Delete old profile picture if it exists**
        if (!empty($user['profile_picture']) && file_exists(__DIR__ . "/../../client/" . $user['profile_picture'])) {
            unlink(__DIR__ . "/../../client/" . $user['profile_picture']);
        }

        $fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($profilePicture['tmp_name'], $targetFilePath)) {
            $profilePicturePath = "images/profile_img/" . $fileName;
        } else {
            throw new Exception('Failed to upload profile picture');
        }
    }


    // Update user information
    $sql = "UPDATE users SET firstName = COALESCE(NULLIF(?, ''), firstName), 
                            lastName = COALESCE(NULLIF(?, ''), lastName), 
                            email = COALESCE(NULLIF(?, ''), email), 
                            address = COALESCE(NULLIF(?, ''), address), 
                            password = ?, 
                            profile_picture = ? 
            WHERE user_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$firstName, $lastName, $email, $address, $hashedPassword, $profilePicturePath, $userId]);

    $pdo->commit();

    // Update session variables
    $_SESSION['firstName'] = $firstName ?: $_SESSION['firstName'];
    $_SESSION['lastName'] = $lastName ?: $_SESSION['lastName'];
    $_SESSION['address'] = $address ?: $_SESSION['address'];
    $_SESSION['profile_picture'] = $profilePicturePath ?? 'images/profile_img/default_profile.jpg';

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
