<?php
    include('../SQL/client_include/friendGroupCount.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/profilePage.css?v=<?php echo time(); ?>">
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
                <img src="<?= $_SESSION['profile_picture'] ?>?v=<?= time(); ?>" alt="prof1">
            </div>
        <div class="info_container">
            <div class="editBtn_Profile">
                <div class="edit_Profbtn">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#editProfile">
                    Edit Profile
                    </button>
                </div>
                    <!-- Modal -->
                    <div class="modal fade" id="editProfile" tabindex="-1" aria-labelledby="addStocksModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addStocksModalLabel">Edit Profile</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="modalForms" action="">
                                    <div>
                                        <label for="file">Choose Profile:</label><br>
                                        <input type="file" name="file" id="file">
                                    </div>
                                    <div>
                                        <label for="firstName">First Name:</label>
                                        <input class="inputS" type="text" id="firstName" name="firstName">
                                    </div>
                                    <div>
                                        <label for="lastName">Last Name:</label>
                                        <input class="inputS" type="text" id="lastName" name="lastName">
                                    </div>
                                    <div>
                                        <label for="email">Email:</label>
                                        <input class="inputS" type="email" id="email" name="email">
                                    </div>
                                    <div>
                                        <label for="address">Address:</label>
                                        <input class="inputS" type="text" id="address" name="address">
                                    </div>
                                    <div>
                                        <label for="currentPassword">Current Password:</label>
                                        <input class="inputS" type="password" id="currentPassword" name="currentPassword">
                                    </div>
                                    <div>
                                        <label for="newPassword">New Password:</label>
                                        <input class="inputS" type="password" id="newPassword" name="newPassword">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="saveProfileChanges">Save changes</button>
                            </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="bio_name">
                <h1><?= $_SESSION["firstName"] . " " . $_SESSION["lastName"] ?></h1>
                <h2><?= $_SESSION["address"]?></h2>
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
    <script src="jsFiles/editProfile.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>