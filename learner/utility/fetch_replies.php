<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "../../utility/db.php";  // Include your database connection

if (isset($_GET['discussion_id'])) {
  $discussion_id = $_GET['discussion_id'];

  // Fetch replies for the discussion
  $query = "SELECT * FROM replies WHERE discussion_id = '$discussion_id' ORDER BY created_at ASC";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo '<div class="reply">';

      // Display the reply content
      echo '<p>' . htmlspecialchars($row['reply_body']) . '</p>';
      echo '<small>Replied by ' . htmlspecialchars($row['user_id']) . ' on ' . $row['created_at'] . '</small>';

      // Display the like and replies icons below each reply
      echo '<div class="reply-actions">';

      // Like icon for the reply
      echo '<span class="like-icon">';
      echo '<i class="fas fa-thumbs-up"></i>';  // Like icon
      echo '</span>';

      // Replies icon for the reply
      echo '<span class="replies-icon">';
      echo '<i class="fas fa-comments"></i> Reply';  // Replies icon
      echo '</span>';

      echo '</div>'; // Close reply-actions div

      echo '</div>'; // Close reply div
    }
  } else {
    echo '<p>No replies yet.</p>';
  }
}
