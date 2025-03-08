<?php
require("../config/DBConnection.php");
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($email);
    $password = validate($password);

    if (empty($email)) {
        header("Location: ../../client/login.php?error=Email is required!");
        exit();
    } 
    if (empty($password)) {
        header("Location: ../../client/login.php?error=Password is required!");
        exit();
    }
    try {
        // Check if admin
        $stmtAdmin = $pdo->prepare("SELECT * FROM admin WHERE adminEmail = ?");
        $stmtAdmin->execute([$email]);
        $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        if ($admin && $password === $admin['adminPassword']) {
            $_SESSION['adminName'] = $admin['adminName'];
            $_SESSION['adminEmail'] = $admin['adminEmail'];
            header("Location: ../../admin/dashboardPage.php");
            exit();
        }

        // Check if user
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmtUser->execute([$email]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Update online status
            $stmtOnline = $pdo->prepare("UPDATE users SET is_online = 1 WHERE email = ?");
            $stmtOnline->execute([$email]);

            $stmtIfOnline = $pdo->prepare("SELECT is_online FROM users WHERE email = ?");
            $stmtIfOnline->execute([$email]);
            $online = $stmtIfOnline->fetch(PDO::FETCH_ASSOC);

            // Check if profile picture is NULL, use default if it is
            $profilePicture = $user['profile_picture'] ?? 'images/profile_img/default_profile.jpg';

            $_SESSION['currentUserId'] = $user['user_id'];
            $_SESSION['firstName'] = $user['firstName'];
            $_SESSION['lastName'] = $user['lastName'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['address'] = $user['address'];
            $_SESSION['profile_picture'] = $profilePicture;
            $_SESSION['online_status'] = $online['is_online'];

            header("Location: ../../client/profilePage.php");
            exit();
        }

        header("Location: ../../client/login.php?error=Wrong login details!");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../client/login.php?error=" . $e->getMessage());
        exit();
    }
}
    
header("Location: ../login.php");
exit();

