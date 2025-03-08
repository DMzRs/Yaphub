<?php
    session_start();
    include_once('authenticate.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/discoverPeoplePage.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Discover People</title>
</head>
<body>
    <?php include('templates/navbar.php'); ?>
    <section class="background">
        <div class="main-container">
            <div class="current-friends-container">
                <div class="current-friends-header">
                    <h1>Current Friends</h1>
                </div>
                <div class="current-friends-list" id="current-friends-list">
                    <!-- Friends will be loaded here -->
                </div>
            </div>
            <div class="search-people-container">
                <div class="search-container">
                    <input type="text" name="search" id="search" placeholder="Search">
                </div>
                <div class="suggested-friends-list" id="suggested-friends-list">
                    <!-- Suggested friends will be loaded here -->
                </div>
            </div>
            <div class="friend-request-container">
                <div class="friend-request-header">
                    <h1>Friend Request</h1>
                </div>
                <div class="friend-request-list" id="friend-request-list">
                    <!-- Friend requests will be loaded here -->
                </div>
            </div>
        </div>
    </section>
<script src="jsFiles/friendsData.js?v=<?php echo time(); ?>"></script>
</body>
</html>