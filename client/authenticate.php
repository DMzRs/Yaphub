<?
if (!isset($_SESSION['currentUserId'])) {
    header("Location: index.php");
    exit;
}