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

  <!-- forum content -->
  <!-- forum content -->
  <main>
    <div class="container forum">
      <!-- Forum Search and Create -->
      <section class="forum-header">
        <h1>Course Forum</h1>

        <!-- Combined Search and Create Post -->
        <div class="forum-header-actions">
          <!-- Search Forum -->
          <div class="forum-search">
            <input type="text" id="search" placeholder="Search the forum...">
            <button id="search-btn">Search</button>
          </div>

          <!-- Create Forum Post Button -->
          <div class="forum-create">
            <button id="create-post-btn">Create Post</button>
          </div>
        </div>

        <!-- Filter Forum Posts -->
        <?php
        // Get the course_id from the URL
        $selected_course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';

        // Fetch courses from the database
        include "../utility/db.php";
        $query = "SELECT course_id, course_title FROM courses";
        $result = mysqli_query($conn, $query);
        ?>

        <div class="forum-filter">
          <label for="course-filter">Filter by Course:</label>
          <select id="course-filter" name="course_id" onchange="filterForum()">
            <option value="">Select Course</option>
            <?php
            // Populate the dropdown and mark the selected course
            while ($row = mysqli_fetch_assoc($result)) {
              $course_id = $row['course_id'];
              $course_title = $row['course_title'];

              // Check if this course is the one selected in the URL
              $selected = ($course_id == $selected_course_id) ? 'selected' : '';
              echo '<option value="' . $course_id . '" ' . $selected . '>' . $course_title . '</option>';
            }
            ?>
          </select>
        </div>

        <script>
          // Redirect to the selected course's forum page on change
          function filterForum() {
            var courseId = document.getElementById('course-filter').value;
            if (courseId) {
              window.location.href = 'forums.php?course_id=' + courseId;
            }
          }
        </script>

      </section>

      <!-- Forum Posts -->
      <section class="forum-posts">
        <div id="forum-posts-list">
          <!-- Display forum posts based on selected course -->
          <?php
          $course_id = $_GET['course_id'];
          $query = "SELECT * FROM discussions WHERE course_id = '$course_id' ORDER BY created_at DESC";
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
            echo '<p>No discussion yet</p>';
          }
          ?>
        </div>
      </section>


    </div>
  </main>

  <!-- Create Post Modal -->
  <div id="create-post-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Create New Post</h2>
      <form action="utility/create_forum_post.php" method="post" onsubmit="copyContentToTextarea()">
        <!-- Title -->
        <div class="modal-field">
          <label for="post-title">Title:</label>
          <input type="text" id="post-title" name="post_title" required>
        </div>

        <!-- Body with Rich Text Editor -->
        <div class="modal-field">
          <label for="post-body">Body:</label>
          <div class="editor-toolbar">
            <button type="button" id="bold-btn">B</button>
            <button type="button" id="italic-btn">I</button>
            <button type="button" id="underline-btn">U</button>
            <!-- Add more formatting options as needed -->
          </div>
          <div contenteditable="true" id="post-body" class="editor"></div>

          <!-- Hidden textarea to store content from contenteditable div -->
          <textarea id="hidden-post-body" name="post_body" style="display:none;"></textarea>
          <script>
            function copyContentToTextarea() {
              var contentEditableDiv = document.getElementById("post-body");
              var hiddenTextarea = document.getElementById("hidden-post-body");

              // Copy the content from the contenteditable div to the hidden textarea
              hiddenTextarea.value = contentEditableDiv.innerHTML;
            }
          </script>
        </div>

        <!-- Course Selection -->
        <?php
        // Get the course_id from the URL
        $selected_course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';

        // Fetch courses from the database
        include "../utility/db.php";
        $query = "SELECT course_id, course_title FROM courses";
        $result = mysqli_query($conn, $query);
        ?>

        <div class="modal-field">
          <label for="modal-course-filter">Select Course:</label>
          <select id="modal-course-filter" name="course_id">
            <option value="">Select Course</option>
            <?php
            // Populate the dropdown and mark the selected course
            while ($row = mysqli_fetch_assoc($result)) {
              $course_id = $row['course_id'];
              $course_title = $row['course_title'];

              // Check if this course is the one selected in the URL
              $selected = ($course_id == $selected_course_id) ? 'selected' : '';
              echo '<option value="' . $course_id . '" ' . $selected . '>' . $course_title . '</option>';
            }
            ?>
          </select>
        </div>


        <!-- Submit Button -->
        <button type="submit">Submit Post</button>
      </form>

    </div>
  </div>

  <script>
    // JavaScript for modal functionality
    const modal = document.getElementById('create-post-modal');
    const btn = document.getElementById('create-post-btn');
    const span = document.getElementsByClassName('close')[0];

    btn.onclick = function() {
      modal.style.display = 'block';
    }

    span.onclick = function() {
      modal.style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    }

    // JavaScript for rich text editor
    document.getElementById('bold-btn').onclick = function() {
      document.execCommand('bold');
    };

    document.getElementById('italic-btn').onclick = function() {
      document.execCommand('italic');
    };

    document.getElementById('underline-btn').onclick = function() {
      document.execCommand('underline');
    };
  </script>

  <?php include "../utility/footer.php"; ?>
  <!-- Script for js and chatbot -->
  <?php include "../utility/bot.php" ?>
  <script src="../js/script.js"></script>
  <!-- forum search functionality -->
  <script>
    document.getElementById('search-btn').addEventListener('click', function() {
      var searchQuery = document.getElementById('search').value;
      var courseId = new URLSearchParams(window.location.search).get('course_id'); // Get course_id from URL
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'utility/search_forum.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        if (xhr.status === 200) {
          document.getElementById('forum-posts-list').innerHTML = xhr.responseText;
        } else {
          console.error('Error fetching search results.');
        }
      };

      xhr.send('query=' + encodeURIComponent(searchQuery) + '&course_id=' + encodeURIComponent(courseId));
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Attach click event to discussion title and replies link
      document.querySelectorAll('.discussion-title, .view-replies').forEach(function(element) {
        element.addEventListener('click', function(event) {
          event.preventDefault();
          const postId = this.getAttribute('data-post-id');
          const postContainer = this.closest('.forum-post');
          let repliesContainer = postContainer.querySelector('.replies-container');

          if (repliesContainer) {
            // Replies container exists, toggle visibility
            if (repliesContainer.classList.contains('visible')) {
              // Hide replies
              repliesContainer.classList.remove('visible');
              repliesContainer.style.display = 'none';
            } else {
              // Show replies
              repliesContainer.classList.add('visible');
              repliesContainer.style.display = 'block';
            }
          } else {
            // Replies container doesn't exist, create and show it
            repliesContainer = document.createElement('div');
            repliesContainer.classList.add('replies-container');
            postContainer.appendChild(repliesContainer);

            fetch(`utility/fetch_replies.php?discussion_id=${postId}`)
              .then(response => response.text())
              .then(data => {
                repliesContainer.innerHTML = data;
                repliesContainer.classList.add('visible');
                repliesContainer.style.display = 'block';
              })
              .catch(error => console.error('Error loading replies:', error));
          }
        });
      });
    });
  </script>

</body>

</html>

<style>
  /* General styles for the forum */
  .forum {
    margin: 20px auto;
    max-width: 800px;
  }

  .forum-header {
    margin-bottom: 20px;
    padding: 10px;
  }

  .forum-header-actions {
    display: flex;
    justify-content: space-between;
    /* align-items: center; */
  }

  .forum-search {
    display: flex;
    align-items: center;
    gap: 10px;
    /* Space between input and button */
    margin-bottom: 20px;
    /* Space below the search bar */
  }

  .forum-search input[type="text"] {
    flex: 1;
    /* Make input take available space */
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
  }

  .forum-search button {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
  }

  .forum-search button:hover {
    background-color: #0056b3;
  }

  .forum-create button {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
  }

  .forum-create button:hover {
    background-color: #0056b3;
  }

  .forum-filter {
    margin-top: 20px;
  }

  .forum-filter select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  .forum-post-actions {
    display: flex;
    margin-top: 10px;
  }

  /* Modal styles */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black background with opacity */
  }

  .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px;
    width: 80%;
    /* Could be more or less, depending on screen size */
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 8px rgba(1, 2, 0, 0.7);
  }

  .modal-content h2 {
    margin-top: 0;
  }

  .modal-field {
    margin-bottom: 15px;
  }

  .modal-field label {
    display: block;
    margin-bottom: 5px;
  }

  .modal-field input[type="text"],
  .modal-field select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  .editor-toolbar {
    margin-bottom: 10px;
  }

  .editor-toolbar button {
    background-color: #007bff;
    border: none;
    color: #fff;
    padding: 5px 10px;
    margin-right: 5px;
    border-radius: 5px;
    cursor: pointer;
  }

  .editor-toolbar button:hover {
    background-color: #0056b3;
  }

  .editor {
    width: 100%;
    min-height: 200px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow-y: auto;
  }

  /* Dark mode styles */
  body.dark-mode {
    background-color: #181818;
    color: #eaeaea;
  }

  body.dark-mode .modal-content {
    background-color: #2e2e2e;
    color: #eaeaea;
  }

  body.dark-mode .forum-search input[type="text"],
  body.dark-mode .forum-create button,
  body.dark-mode .forum-filter select,
  body.dark-mode .modal-field input[type="text"],
  body.dark-mode .modal-field select {
    background-color: #333;
    color: #eaeaea;
    border: 1px solid #444;
  }

  body.dark-mode button {
    background-color: #333;
    color: #eaeaea;
  }

  body.dark-mode .editor-toolbar button {
    background-color: #333;
    color: #eaeaea;
  }

  body.dark-mode .editor-toolbar button:hover {
    background-color: #444;
  }

  body.dark-mode .close {
    color: #bbb;
  }

  body.dark-mode .editor-btn {
    border: 1px solid #555;
    color: #e0e0e0;
  }

  body.dark-mode .editor-btn:hover {
    background-color: #007bff;
    color: #fff;
  }

  /* Forum Posts Container */
  .forum-posts {
    margin-top: 20px;
    padding: 10px;
  }

  /* Individual Forum Post */
  .forum-post {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    transition: background-color 0.3s, box-shadow 0.3s;
  }

  /* Forum Post Title */
  .forum-post h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 10px;
  }

  /* Forum Post Body */
  .forum-post p {
    font-size: 1rem;
    line-height: 1.6;
    color: #555;
  }

  /* Forum Post Meta Information (User and Date) */
  .forum-post small {
    display: block;
    font-size: 0.9rem;
    color: #888;
    margin-top: 15px;
  }

  /* Hover Effects for Forum Post */
  .forum-post:hover {
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  /* Dark Mode Styles */
  body.dark-mode .forum-post {
    background-color: #333;
    border-color: #555;
  }

  body.dark-mode .forum-post h2,
  body.dark-mode .forum-post p,
  body.dark-mode .forum-post small {
    color: #ccc;
  }

  body.dark-mode .forum-post:hover {
    background-color: #444;
  }

  /* Replies Container */
  .reply {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background-color: #f8f9fa;
  }

  .reply-actions {
    display: flex;
    margin-top: 10px;
    font-size: 14px;
    /* Adjust font size to control icon size */
  }

  .like-icon,
  .replies-icon {
    display: inline-flex;
    align-items: center;
    margin-right: 20px;
    /* Space between icons */
    color: #007bff;
    /* Color for the icons */
    cursor: pointer;
    /* Cursor change on hover */
  }

  .like-icon i,
  .replies-icon i {
    font-size: 18px;
    /* Size of the icons */
    margin-right: 5px;
    /* Space between icon and text */
  }

  /* Dark mode adjustments */
  body.dark-mode .like-icon,
  body.dark-mode .replies-icon {
    color: #17a2b8;
    /* Color change for dark mode */
  }

  /* 
  .reply small {
    display: block;
    margin-top: 5px;
    color: #6c757d;
  } */

  /* Dark mode */
  body.dark-mode .reply {
    background-color: #343a40;
    border-color: #454d55;
    color: #f8f9fa;
  }

  /* body.dark-mode .reply small {
    color: #adb5bd;
  } */

  body.dark-mode .like-icon,
  body.dark-mode .replies-icon {
    color: #17a2b8;
  }

  .replies-container {
    display: none;
    margin-top: 10px;
  }

  .replies-container.visible {
    display: block;
  }
</style>