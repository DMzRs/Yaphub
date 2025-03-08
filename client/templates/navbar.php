<head>
    <title>YapHub</title>
    <link rel="stylesheet" href="styles/navbar.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
    <body>
    <ul class="nav flex-column">
        <li class="nav-item logo">
            <img src="images/logo/yaphub_logo.png" alt="logo">
        </li>
        <li class="nav-item main">
            <a class="nav-link" href="profilePage.php"><img src="images/icons/profile_icon.png" alt="profile" class="iconbtn"><br>Profile</a>
        </li>
        <li class="nav-item main">
            <a class="nav-link" href="discoverPeoplePage.php"><img src="images/icons/discoverpeople_icon.png" alt="discoverpeople" class="iconbtn"><br>Discover People</a>
        </li>
        <li class="nav-item main">
            <a class="nav-link" href="messagePage.php"><img src="images/icons/message_icon.png" alt="messages" class="iconbtn"><br>Messages</a>
        </li>
        <li class="nav-item main">
            <a class="nav-link" href="createGroupPage.php"><img src="images/icons/groupchat_icon.png" alt="groupchat" class="iconbtn"><br>Create Group</a>
        </li>
        <li class="nav-item logout">
            <a class="nav-link" href="../SQL/config/logout.php"><img src="images/icons/logout_icon.png" class="logout_icon" width="20px" height="20px">Logout</a>
        </li>
    </ul>