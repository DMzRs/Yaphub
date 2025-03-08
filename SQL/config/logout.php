<?php
    session_start();
    include('DBConnection.php');
        $stmtOnline = $pdo->prepare("UPDATE users SET is_online = 0 WHERE email =?");
        $stmtOnline->execute([$_SESSION['email']]);
    session_destroy();
    header("Location: ../../client/login.php");
?>