<section class="forum-posts">
  <div id="forum-posts-list">
    <?php
    $course_id = $_GET['course_id'];

    // Query to fetch discussions along with usernames
    $query = "
            SELECT d.*, u.username 
            FROM discussions d
            JOIN users u ON d.user_id = u.user_id
            WHERE d.course_id = '$course_id'
            ORDER BY d.created_at DESC
        ";
    $result = mysqli_query($conn, $query);

    // Check if there are any results
    if (mysqli_num_rows($result) > 0) {
      // Loop through and display forum posts
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
        echo '<small>Posted by ' . htmlspecialchars($row['username']) . ' on ' . $row['created_at'] . '</small>';

        // Like icon and replies count
        echo '<div class="forum-post-actions">';
        echo '<span class="like-icon"><i class="fas fa-thumbs-up"></i></span>';
        echo '<span class="replies-icon"> <i class="fas fa-comments"></i> <a href="#" class="view-replies" data-post-id="' . $discussion_id . '">';
        echo $replies . ' Replies</a></span>';
        echo '</div>';

        // Container for replies
        echo '<div id="replies-container-' . $discussion_id . '" class="replies-container" style="display:none;"></div>';

        echo '</div>';
      }
    } else {
      // If no posts are found, display message
      echo '<div>';
      echo '<p>No discussion yet</p>';
      echo '</div>';
    }
    ?>
  </div>
</section>