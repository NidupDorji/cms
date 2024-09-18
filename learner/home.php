<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

  <!-- <?php echo $_SESSION['user_id'] ?> -->
  <!-- welcome msg using welcome.php -->
  <?php include "../utility/welcome.php" ?>

  <!-- display top 3 courses. -->

  <!-- DISPLAY AVAILABLE/UPLOADED TOP COURSES -->
  <?php include "utility/navigation.php" ?>

  <!-- Conditionally include top courses only if there's no search query -->
  <?php
  // Get the search query from the URL
  $searchQuerylo = isset($_GET['query']) ? trim($_GET['query']) : '';

  // Check if the search query is empty
  if (empty($searchQuerylo)) {
    // If no search query is present, include top courses
    include "utility/top_courses.php";
  }
  ?>

  <!-- Footer -->
  <?php include "../utility/footer.php"; ?>
  <!-- Script for js and chatbot -->
  <?php include "../utility/bot.php" ?>
  <script src="../js/script.js"></script>
</body>

</html>