<?php
    require("../SQL/config/DBConnection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/login.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>YapHub</title>
</head>
<body>
    <section class="separator">
        <div class="logo">
            <img src="images/logo/yaphub_logo.png" alt="logo">
        </div>
    <section class="login">
    <section class="createaccount">
        <div class="forms">
            <h1>Create Account</h1>
            <?php if (isset($_GET['error'])): ?>
                <h2 class="error"><?php echo htmlspecialchars($_GET['error']); ?></h2>
            <?php endif; ?>
            <form class="formInput" action="../SQL/dbquery/signupDB.php" method="POST">
                <input class="inputs" type="text" name="firstName" id="firstName" placeholder="First Name">
                <input class="inputs" type="text" name="lastName" id="lastName" placeholder="Last Name">
                <input class="inputs" type="email" name="email" id="email" placeholder="Email">
                <input class="inputs" type="text" name="address" id="address" placeholder="Address">
                <input class="inputs" type="password" name="password" id="password" placeholder="Password">
                <input class="submitbtn" type="submit" name="submit" id="submit" placeholder="Create Account">
            </form>
            <h3>Already have an account? <a href="login.php">Login here</a></h3>
        </div>
    </section>
    </section>
    </section>
</body>
</html>