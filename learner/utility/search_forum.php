<?php
// Include database connection
include "../../utility/db.php";

// Get the search query and course_id from POST request
$searchQuery = isset($_POST['query']) ? mysqli_real_escape_string($conn, $_POST['query']) : '';
$course_id = isset($_POST['course_id']) ? mysqli_real_escape_string($conn, $_POST['course_id']) : '';

// Prepare the SQL query
$query = "SELECT * FROM discussions WHERE course_id = '$course_id' AND (discussion_title LIKE '%$searchQuery%' OR discussion_body LIKE '%$searchQuery%') ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Check if there are any results
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $discussion_id = $row['discussion_id'];

    // Replies count
    $replies_query = "SELECT COUNT(*) AS replies_count FROM replies WHERE discussion_id = '$discussion_id'";
    $replies_result = mysqli_query($conn, $replies_query);
    $replies = mysqli_fetch_assoc($replies_result)['replies_count'];

    // Render post
    echo '<div class="forum-post">';

    // Discussion title wrapped in anchor tag
    echo '<a href="#" class="discussion-title" data-post-id="' . $discussion_id . '">';
    echo '<h2>' . htmlspecialchars($row['discussion_title']) . '</h2>';
    echo '</a>';

    // Post content
    echo '<p>' . htmlspecialchars($row['discussion_body']) . '</p>';
    echo '<small>Posted by ' . htmlspecialchars($row['user_id']) . ' on ' . $row['created_at'] . '</small>';

    // Like icon and replies count
    echo '<div class="forum-post-actions">';
    echo '<span class="like-icon"><i class="fas fa-thumbs-up"></i></span>';
    echo '<span class="replies-icon"><i class="fas fa-comments"></i><a href="#" class="view-replies" data-post-id="' . $discussion_id . '">';
    echo $replies . ' Replies</a></span>';
    echo '</div>';

    echo '</div>';
  }
} else {
  // If no posts are found, display message
  echo '<p>No matching discussions</p>';
}
