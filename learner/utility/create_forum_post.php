<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include "../../utility/db.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the form data
  $user_id = $_SESSION['user_id']; // Assuming the user_id is stored in the session
  $course_id = $_POST['course_id'];
  $discussion_title = mysqli_real_escape_string($conn, $_POST['post_title']);
  $discussion_body = mysqli_real_escape_string($conn, $_POST['post_body']);

  // Insert into the discussions table
  $query = "INSERT INTO discussions (course_id, user_id, discussion_title, discussion_body, created_at)
              VALUES ('$course_id', '$user_id', '$discussion_title', '$discussion_body', NOW())";

  if (mysqli_query($conn, $query)) {
    // Redirect to forums.php after successful post creation
    header("Location: ../forums.php?course_id=" . $course_id);
    exit();
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
