<?php
    include('../SQL/client_include/viewFriendProfile.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/viewFriendProfilePage.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Profile</title>
</head>
<body>
    <?php include('templates/navbar.php'); ?>
    <section class="background">
        <div class="profile">
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>?v=<?php echo time(); ?>" alt="Profile Picture">
        </div>
        <div class="info_container">
            <div class="buttons">
                    <div class="add-or-removebtn">
                        <?php if ($friendshipStatus === 'accepted'): ?>
                            <form action="../SQL/dbquery/unfriend.php" method="POST">
                                <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <button type="submit">Unfriend</button>
                            </form>
                        <?php elseif ($friendshipStatus === 'pending'): ?>
                            <button disabled>Request Pending</button>
                        <?php else: ?>
                            <form action="../SQL/dbquery/addFriend.php" method="POST">
                                <input type="hidden" name="add_friend_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <button type="submit">Add Friend</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="message-btn">
                        <?php if ($friendshipStatus === 'accepted'): ?>
                            <button id="openChatBtn">Message</button>
                        <?php endif; ?>
                    </div>
            </div>
            <div class="bio_name">
                <h1><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h1>
                <h2><?= htmlspecialchars($user["address"]); ?></h2>
            </div>
            <div class="status">
                <h2><?= $user['is_online'] ? 'Online' : 'Offline'; ?></h2>
            </div>
            <div class="current">
                <div class="current_group">
                    <h2>Current Groups</h2>
                    <h1><?php echo htmlspecialchars($groupCount); ?></h1>
                </div>
                <div class="vl"></div>
                <div class="current_friends">
                    <h2>Current Friends</h2>
                    <h1><?php echo htmlspecialchars($friendCount); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <script>
    var chatId = <?= isset($chat_id) ? json_encode($chat_id) : "null" ?>; // Convert PHP to JS

    document.getElementById("openChatBtn").addEventListener("click", function () {
        if (chatId) {
            window.location.href = `messagePage.php?chat_id=${chatId}`;
        } else {
            alert("No chat found with this user!");
        }
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>