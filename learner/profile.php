<?php
session_start();

//authentication check using auth.php
include "../utility/auth.php";
?>
<!DOCTYPE html>
<html lang="en">

<!-- head from head.php -->
<?php include "utility/head.php" ?>

<body>
  <!-- header -->
  <?php include "utility/header.php" ?>

  <!--  USER DETAILS-->
  <div class="container">
    <div class="profile-info">
      <h2>User Profile</h2>
      <div class="profile-picture">
        <img src="../profilePic/profile.jpeg" alt="Profile Picture">
      </div>
      <div class="personal-details">
        <h3>Details</h3>
        <p><strong>Name:</strong> <?php echo $_SESSION['username']; ?> </p>
        <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?> </p>
        <h3>Courses Taken</h3>
        <ul>
          <li>Course 1</li>
          <li>Course 2</li>
          <li>Course 3</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include "../utility/footer.php"; ?>
  <!-- Script for js and chatbot -->
  <?php include "../utility/bot.php" ?>
  <script src="../js/script.js"></script>
</body>

</html>