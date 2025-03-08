<head>
    <title>YapHub</title>
    <link rel="stylesheet" href="adminStyles/adminNavbar.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
    <body>
    <ul class="nav flex-column">
        <li class="nav-item logo">
            <img src="adminImages/logo/yaphub_logo.png" alt="logo">
            <h1 class="adminHeader">ADMIN</h1>
        </li>
        <li class="nav-item main dashboard">
            <a class="nav-link" href="dashboardPage.php"><img src="adminImages/icons/dashboard_icon.png" alt="profile" class="iconbtn"> Dashboard</a>
        </li>
        <li class="nav-item logout">
            <a class="nav-link" href="../SQL/config/logout.php"><img src="adminImages/icons/logout_icon.png" class="logout_icon" width="20px" height="20px">Logout</a>
        </li>
    </ul>