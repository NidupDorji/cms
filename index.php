<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $_SESSION['role'] = $_POST['role'];

  // Redirect based on selected role
  if ($_SESSION['role'] == 'admin') {
    header("Location: admin/login.php");
  } elseif ($_SESSION['role'] == 'teacher') {
    header("Location: teacher/login.php");
  } elseif ($_SESSION['role'] == 'learner') {
    header("Location: learner/login.php");
  } else {
    echo "Invalid role selected.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Role</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="role-selection-container">
    <h1>Select Your Role</h1>
    <form action="index.php" method="post">
      <div class="role-card">
        <input type="radio" name="role" value="admin" id="admin" required>
        <label for="admin">
          <div class="role-icon">👑</div>
          <h2>Admin</h2>
          <p>Manage users, courses, and overall website settings.</p>
        </label>
      </div>
      <div class="role-card">
        <input type="radio" name="role" value="teacher" id="teacher" required>
        <label for="teacher">
          <div class="role-icon">🎓</div>
          <h2>Teacher</h2>
          <p>Create and manage course content for learners.</p>
        </label>
      </div>
      <div class="role-card">
        <input type="radio" name="role" value="learner" id="learner" required>
        <label for="learner">
          <div class="role-icon">📘</div>
          <h2>Learner</h2>
          <p>Access and interact with course content.</p>
        </label>
      </div>
      <button type="submit" class="submit-btn">Continue</button>
    </form>
  </div>
  <script>
    document.querySelectorAll('.role-card').forEach(card => {
      card.addEventListener('click', function() {
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
      });
    });
  </script>
</body>

</html>