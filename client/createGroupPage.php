<?php
  session_start();
  include_once('authenticate.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/createGroupPage.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Create Group</title>
</head>
<body>
  <?php include('templates/navbar.php'); ?>
    <section class="background">
      <div class="list-container">
            <div class="first-container">
              <div class="groupName-container">
                <form action="">
                  <input type="text" name="groupName" id="groupName" placeholder="Group Name" required>
                </form>
              </div>
              <div class="create-group">
                  <button class="createGroupbtn" name="createGroup">Create Group</button>
              </div>
            </div>
            <div class="error_message">
              
            </div>
            <div class="second-container">
              <div class="search-members-container">
                <div class="search-members">
                    <form action="">
                      <input type="text" name="searchMember" id="searchMember" placeholder="Search for a user" required>
                    </form>
                </div>
                <div class="show-users">
                  <!-- diri sulod ang wa na add -->
                </div>
              </div>
              <div class="members-container">
                <div class="memberTitle">
                  <h2>Members:</h2>
                </div>
                <div class="added-members">
                  <!-- diri sulod ang na add -->
                </div> 
              </div>
            </div>
      </div>
    </section>
    <script src="jsFiles/createGroup.js"></script>
</body>
</html>