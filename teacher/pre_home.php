<?php
include('server.php');
//session_start();  // Start the session

// Optional: Check if the user is coming from the registration process
// if (!isset($_SESSION['registration_success'])) {
//     header('Location: index.php');
//     exit();
// }
?>

<!DOCTYPE html>
<html>

<head>
  <title>🎓Pre-index Page</title>
  <!-- Uncomment the line below to include your CSS file -->
  <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>

<body>
  <div class="content">
    <h2>Congratulation "<?php echo $_SESSION['username']; ?>", you have registered successfully.</h2>
    <p style="text-align: center; font-size: 18px; color: #333; padding-bottom:5%;">
      Welcome TEACHER.
    </p>
    <p style="text-align: center;">
      <a href="home.php" class="btn">Proceed to Homepage</a>
    </p>
  </div>
</body>

</html>