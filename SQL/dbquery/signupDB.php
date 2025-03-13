<?php
require("../config/DBConnection.php");

if (isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $firstName = validate($firstName);
    $lastName = validate($lastName);
    $email = validate($email);
    $address = validate($address);
    $password = validate($password);

    if (empty($firstName)) {
        header("Location: ../../client/index.php?error=First Name is required!");
        exit();
    } 
    if (empty($lastName)) {
        header("Location: ../../client/index.php?error=Last Name is required!");
        exit();
    } 
    if (empty($email)) {
        header("Location: ../../client/index.php?error=Email is required!");
        exit();
    } 
    if (empty($address)) {
        header("Location: ../../client/index.php?error=Address is required!");
        exit();
    } 
    if (empty($password)) {
        header("Location: ../../client/index.php?error=Password is required!");
        exit();
    }
    if (strlen($password) < 8) {
        header("Location: ../../client/index.php?error=Password must be at least 8 characters!");
        exit();
    }
    
    try {
        // Check if email already exists in users table
        $stmtUser = $pdo->prepare("SELECT email FROM users WHERE email = ?");
        $stmtUser->execute([$email]);
        $checkEmailUser = $stmtUser->rowCount();

        // Check if email already exists in admin table
        $stmtAdmin = $pdo->prepare("SELECT adminEmail FROM admin WHERE adminEmail = ?");
        $stmtAdmin->execute([$email]);
        $checkEmailAdmin = $stmtAdmin->rowCount();

        if ($checkEmailUser > 0) {
            header("Location: ../../client/index.php?error=Email is already existing!");
            exit();
        } elseif ($checkEmailAdmin > 0) {
            header("Location: ../../client/index.php?error=This email is for admin!");
            exit();
        } else {
            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into users table
            $stmtCreateUser = $pdo->prepare("INSERT INTO users (firstName, lastName, email, address, password) VALUES (?, ?, ?, ?, ?)");
            if ($stmtCreateUser->execute([$firstName, $lastName, $email, $address, $hashedPassword])) {
                header("Location: ../../client/login.php");
                exit();
            } else {
                header("Location: ../../client/index.php?error=Error Occurred In Executing Statement!");
                exit();
            }
        }
    } catch (PDOException $e) {
        header("Location: ../../client/index.php?error=" . $e->getMessage());
        exit();
    }   
} 

header("Location: ../../client/login.php");
exit();
