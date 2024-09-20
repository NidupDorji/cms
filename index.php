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
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>Select Role</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="role-selection-container">
    <div class="form-container">
      <form class="index-form" action="index.php" method="post">
        <div class="role-card" data-role="admin">
          <input type="radio" name="role" value="admin" id="admin" required>
          <label for="admin">
            <div class="role-icon">👑</div>
            <h3>Admin</h3>
            <p>Manage users, courses, and overall website settings.</p>
          </label>
        </div>
        <div class="role-card" data-role="teacher">
          <input type="radio" name="role" value="teacher" id="teacher" required>
          <label for="teacher">
            <div class="role-icon">🎓</div>
            <h3>Teacher</h3>
            <p>Create and manage course content for learners.</p>
          </label>
        </div>
        <div class="role-card" data-role="learner">
          <input type="radio" name="role" value="learner" id="learner" required>
          <label for="learner">
            <div class="role-icon">📘</div>
            <h3>Learner</h3>
            <p>Access and interact with course content.</p>
          </label>
        </div>
      </form>
    </div>
  </div>
  <script>
    document.querySelectorAll('.role-card').forEach(card => {
      card.addEventListener('dblclick', function() {
        // Set the role input based on the clicked card
        const role = this.getAttribute('data-role');
        document.querySelector(`input[name="role"][value="${role}"]`).checked = true;

        // Submit the form
        this.closest('form').submit();
      });

      card.addEventListener('click', function() {
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
      });
    });
    // Detect if the user is on a mobile device
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    document.querySelectorAll('.role-card').forEach(card => {
      // For both mobile and desktop
      const selectRole = function() {
        // Set the role input based on the clicked card
        const role = this.getAttribute('data-role');
        document.querySelector(`input[name="role"][value="${role}"]`).checked = true;

        // Submit the form
        this.closest('form').submit();
      };

      // Add single-click selection for mobile
      if (isMobile) {
        card.addEventListener('click', selectRole);
      } else {
        // Double-click for desktop, single click for selecting the card
        card.addEventListener('dblclick', selectRole);

        card.addEventListener('click', function() {
          document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
          this.classList.add('selected');
        });
      }
    });
  </script>
</body>

</html>