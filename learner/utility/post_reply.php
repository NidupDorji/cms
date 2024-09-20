<?php
session_start();

include "../../utility/db.php";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the discussion ID, user ID, and reply body from the form
  $discussion_id = isset($_POST['discussion_id']) ? mysqli_real_escape_string($conn, $_POST['discussion_id']) : null;
  $user_id = isset($_POST['user_id']) ? mysqli_real_escape_string($conn, $_POST['user_id']) : null;
  $reply_body = isset($_POST['reply_body']) ? mysqli_real_escape_string($conn, $_POST['reply_body']) : null;

  // Check if the parent reply ID is set (for nested replies)
  $parent_reply_id = isset($_POST['parent_reply_id']) ? mysqli_real_escape_string($conn, $_POST['parent_reply_id']) : 0;

  // Ensure all necessary data is available
  if ($discussion_id && $user_id && $reply_body) {
    // Insert the reply into the 'replies' table
    $query = "
            INSERT INTO replies (discussion_id, user_id, reply_body, parent_reply_id, created_at) 
            VALUES ('$discussion_id', '$user_id', '$reply_body', '$parent_reply_id', NOW())
        ";

    // Execute the query and check for errors
    if (mysqli_query($conn, $query)) {
      // Redirect back to the previous page
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    } else {
      echo "Error: " . mysqli_error($conn);
    }
  } else {
    echo "All fields are required!";
  }
}
