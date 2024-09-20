<?php
session_start();
include "../../utility/db.php";

if (isset($_GET['discussion_id'])) {
  $discussion_id = $_GET['discussion_id'];

  // Fetch discussion details
  $query = "
        SELECT d.*, u.username 
        FROM discussions d
        JOIN users u ON d.user_id = u.user_id
        WHERE d.discussion_id = '$discussion_id'
    ";
  $discussion_result = mysqli_query($conn, $query);
  $discussion = mysqli_fetch_assoc($discussion_result);

  if ($discussion) {
    // Replies count
    $replies_query = "SELECT COUNT(*) AS replies_count FROM replies WHERE discussion_id = '$discussion_id'";
    $replies_result = mysqli_query($conn, $replies_query);
    $replies = mysqli_fetch_assoc($replies_result)['replies_count'];

    // Display post content
    echo '<div class="post-content">';
    echo '<h2>' . htmlspecialchars($discussion['discussion_title']) . '</h2>';
    echo '<p>' . htmlspecialchars($discussion['discussion_body']) . '</p>';
    echo '<small>Posted by ' . htmlspecialchars($discussion['username']) . ' on ' . $discussion['created_at'] . '</small>';

    // Like icon and replies count
    echo '<div class="r-forum-post-actions">';
    echo '<span class="r-like-icon"><i class="fas fa-thumbs-up"></i></span>';
    echo '<span class="r-replies-icon"><i class="fas fa-comments"></i> <a href="#" class="r-view-replies" data-post-id="' . $discussion_id . '">';
    echo $replies . ' Replies</a></span>';
    echo '</div>';
    echo '</div>';

    // 'Reply form' (hidden by default)
    echo '<div class="reply-form" id="reply-form1-' . $discussion_id . '" style="display: none;">';
    echo '<form action="utility/post_reply.php" method="post">';
    echo '<input type="hidden" name="discussion_id" value="' . htmlspecialchars($discussion_id) . '">';
    echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['user_id']) . '">';
    echo '<textarea name="reply_body" placeholder="Write your reply..." required></textarea>';
    echo '<button type="submit">Reply</button>';
    echo '</form>';
    echo '</div>';

    // Fetch first-level replies
    $replies_query = "
            SELECT r.*, u.username 
            FROM replies r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.discussion_id = '$discussion_id' AND r.parent_reply_id = 0
            ORDER BY r.created_at ASC
        ";
    $replies_result = mysqli_query($conn, $replies_query);

    echo '<div class="replies" id="replies1-' . $discussion_id . '" style="display: block;">';

    if (mysqli_num_rows($replies_result) > 0) {
      while ($reply = mysqli_fetch_assoc($replies_result)) {
        // Count replies for the current reply
        $count_replies_query = "
                    SELECT COUNT(*) AS replies_count 
                    FROM replies 
                    WHERE parent_reply_id = '{$reply['reply_id']}'
                ";
        $count_replies_result = mysqli_query($conn, $count_replies_query);
        $replies_count = mysqli_fetch_assoc($count_replies_result)['replies_count'];

        // Display the 'first-reply'
        echo '<div class="reply">';
        echo '<p>' . htmlspecialchars($reply['reply_body']) . '</p>';
        echo '<small>Replied by ' . htmlspecialchars($reply['username']) . ' on ' . $reply['created_at'] . '</small>';

        // Like icon, replies count and Settings (three dots) icon
        echo '<div class="forum-post-actions">';
        echo '<span class="like-icon"><i class="fas fa-thumbs-up"></i></span>';
        echo '<span class="replies-icon"><i class="fas fa-comments"></i> <a href="#" class="rr-view-replies" data-reply-id="' . $reply['reply_id'] . '">';
        echo $replies_count . ' Replies</a></span>';
        echo '<div class="settings-icon" style="display: inline-block; position: relative;">';
        echo '<i class="fas fa-ellipsis-h"></i>';

        // Dropdown menu for settings (initially hidden)
        echo '<div class="settings-menu" style="display: none; position: absolute; right: 0; background-color: white; border: 1px solid #ccc; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">';
        echo '<ul style="list-style: none; margin: 0; padding: 0;">';
        echo '<li><a href="#" class="report-reply" data-reply-id="' . $reply['reply_id'] . '">Report</a></li>';
        echo '<li><a href="#" class="share-reply" data-reply-id="' . $reply['reply_id'] . '">Share</a></li>';
        echo '</ul>';
        echo '</div>'; // Close settings menu
        echo '</div>'; // Close settings icon container
        echo '</div>'; // Close forum-post-actions

        // 'Reply form' (hidden by default) for this reply
        echo '<div class="reply-form" id="reply-form-' . $reply['reply_id'] . '" style="display: none;">';
        echo '<form action="utility/post_reply.php" method="post">';
        echo '<input type="hidden" name="discussion_id" value="' . htmlspecialchars($discussion_id) . '">';
        echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['user_id']) . '">';
        echo '<input type="hidden" name="parent_reply_id" value="' . htmlspecialchars($reply['reply_id']) . '">';
        echo '<textarea name="reply_body" placeholder="Write your reply..." required></textarea>';
        echo '<button type="submit">Post Reply</button>';
        echo '</form>';
        echo '</div>'; // Close reply-form

        // Fetch and display nested (second-level) replies
        if ($replies_count > 0) {
          fetchNestedReplies($reply['reply_id'], $conn);
        }

        echo '</div>'; // Close reply
      }
    } else {
      echo '<div class="reply">';
      echo '<p>No replies yet.</p>';
      echo '</div>'; // Close reply
    }
    echo '</div>'; // Close replies container
  } else {
    echo '<p>Post not found.</p>';
  }
}

// Function to fetch and display nested replies
function fetchNestedReplies($parent_reply_id, $conn)
{
  $nested_replies_query = "
        SELECT r.*, u.username 
        FROM replies r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.parent_reply_id = '$parent_reply_id'
        ORDER BY r.created_at ASC
    ";
  $nested_replies_result = mysqli_query($conn, $nested_replies_query);

  if (mysqli_num_rows($nested_replies_result) > 0) {
    echo '<div class="nested-replies">';
    while ($nested_reply = mysqli_fetch_assoc($nested_replies_result)) {
      echo '<div class="reply">';
      echo '<p>' . htmlspecialchars($nested_reply['reply_body']) . '</p>';
      echo '<small>Replied by ' . htmlspecialchars($nested_reply['username']) . ' on ' . $nested_reply['created_at'] . '</small>';

      // Like icon, replies count, and settings for nested replies
      echo '<div class="forum-post-actions">';
      echo '<span class="like-icon"><i class="fas fa-thumbs-up"></i></span>';
      echo '<span class="replies-icon"><i class="fas fa-comments"></i></span>';
      echo '<div class="settings-icon" style="display: inline-block; position: relative;">';
      echo '<i class="fas fa-ellipsis-h"></i>';
      echo '<div class="settings-menu" style="display: none; position: absolute; right: 0; background-color: white; border: 1px solid #ccc;">';
      echo '<ul>';
      echo '<li><a href="#" class="report-reply" data-reply-id="' . $nested_reply['reply_id'] . '">Report</a></li>';
      echo '<li><a href="#" class="share-reply" data-reply-id="' . $nested_reply['reply_id'] . '">Share</a></li>';
      echo '</ul>';
      echo '</div>'; // Close settings menu
      echo '</div>'; // Close settings icon container
      echo '</div>'; // Close forum-post-actions
      echo '</div>'; // Close reply

      // Recursively fetch further nested replies
      $further_nested_replies_count = "
                SELECT COUNT(*) AS replies_count 
                FROM replies 
                WHERE parent_reply_id = '{$nested_reply['reply_id']}'
            ";
      $further_nested_result = mysqli_query($conn, $further_nested_replies_count);
      $nested_count = mysqli_fetch_assoc($further_nested_result)['replies_count'];

      if ($nested_count > 0) {
        fetchNestedReplies($nested_reply['reply_id'], $conn);
      }
    }
    echo '</div>'; // Close nested-replies container
  }
}
