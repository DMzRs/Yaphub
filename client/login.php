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
        <div class="forms">
            <h1>Login</h1>
            <?php if (isset($_GET['error'])): ?>
                <h2 class="error"><?php echo htmlspecialchars($_GET['error']); ?></h2>
            <?php endif; ?>
            <form class="formInput" action="../SQL/dbquery/loginDB.php" method="POST">
                <input class="inputs" type="email" name="email" id="email" placeholder="Email" >
                <input class="inputs" type="password" name="password" id="password" placeholder="Password" >
                <input class="submitbtn" type="submit" name="submit" id="submit" value = "Login">
            </form>
            <h3>Don't have an account? <a href="index.php">Create an account</a></h3>
        </div>
    </section>
    </section>
</body>
</html>