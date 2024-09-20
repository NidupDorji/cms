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
  <?php include "../utility/welcome.php" ?>

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
        <div id="content-container" style="display: none;"></div>

      </section>

      <!-- Forum Posts -->
      <?php include "utility/forum_posts.php" ?>

    </div>
  </main>
  <!-- modal for reply corresponding to the post you clicked -->
  <div id="post-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div id="modal-body">
        <!-- Post content and replies will be loaded here -->
      </div>
    </div>
  </div>

  <!-- Create New Post Modal -->
  <div id="create-post-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Create New Post</h2>
      <form action="utility/create_forum_post.php" method="post" onsubmit="copyContentToTextarea()">
        <!-- Title -->
        <div class="modal-field">
          <label for="post-title">Title:</label>
          <input type="text" id="post-title" name="post_title" placeholder="Write a descriptive title" required>
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


        <!-- Body with Rich Text Editor -->
        <div class="modal-field">
          <label for="post-body">Body:</label>
          <div contenteditable="true" id="post-body" class="editor"></div>
          <div class="editor-toolbar">
            <button type="button" id="bold-btn">B</button>
            <button type="button" id="italic-btn">I</button>
            <button type="button" id="underline-btn">U</button>
            <!-- Add more formatting options as needed -->
            <!-- Embed Image -->
            <button type="button" id="image-btn" title="Insert Image"><i class="fas fa-image"></i></button>

            <!-- Insert Math -->
            <button type="button" id="math-btn" title="Insert Math"><i class="fas fa-square-root-alt"></i></button>

            <!-- Insert Link -->
            <button type="button" id="link-btn" title="Insert Link"><i class="fas fa-link"></i></button>

            <!-- Bulleted List -->
            <button type="button" id="bullet-btn" title="Bulleted List"><i class="fas fa-list-ul"></i></button>

            <!-- Codeblock -->
            <button type="button" id="codeblock-btn" title="Code Block"><i class="fas fa-code"></i></button>

            <!-- Subscript -->
            <button type="button" id="subscript-btn">X<sub>2</sub></button>

            <!-- Superscript -->
            <button type="button" id="superscript-btn">X<sup>2</sup></button>
          </div>
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
        <!-- Submit Button -->
        <button id="submito" type="submit">Submit Post</button>
      </form>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle settings menu on click of the three dots (ellipsis icon)
      document.addEventListener('click', function(event) {
        if (event.target.classList.contains('fa-ellipsis-h')) {
          const settingsIcon = event.target.parentElement;
          const settingsMenu = settingsIcon.querySelector('.settings-menu');

          // Toggle visibility of the settings menu
          if (settingsMenu.style.display === 'none' || !settingsMenu.style.display) {
            settingsMenu.style.display = 'block'; // Show the menu
          } else {
            settingsMenu.style.display = 'none'; // Hide the menu
          }
        }
      });

      // Hide settings menu when clicking outside
      document.addEventListener('click', function(event) {
        if (!event.target.closest('.settings-icon')) {
          const settingsMenus = document.querySelectorAll('.settings-menu');
          settingsMenus.forEach(function(menu) {
            menu.style.display = 'none'; // Hide all menus
          });
        }
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Handle clicks on the 'r-view-replies' class
      document.addEventListener('click', function(event) {
        if (event.target.classList.contains('r-view-replies')) {
          event.preventDefault();
          const discussionId = event.target.getAttribute('data-post-id');

          // Get the reply form and replies section for the specific discussion
          const replyForm = document.getElementById('reply-form1-' + discussionId);
          const repliesSection = document.getElementById('replies1-' + discussionId);

          if (replyForm) {
            // Toggle visibility: if reply form is hidden, show it and hide replies
            if (replyForm.style.display === 'none' || !replyForm.style.display) {
              replyForm.style.display = 'block'; // Show reply form
              // repliesSection.style.display = 'none'; // Hide replies
            } else {
              replyForm.style.display = 'none'; // Hide reply form
              // repliesSection.style.display = 'block'; // Show replies
            }
          } else {
            console.error('Reply form or replies section not found for discussion ID:', discussionId);
          }
        }
      });

      // Handle clicks on the 'view-replies' class
      document.addEventListener('click', function(event) {
        if (event.target.classList.contains('rr-view-replies')) {
          event.preventDefault();
          const replyId = event.target.getAttribute('data-reply-id');
          const replyForm = document.getElementById('reply-form-' + replyId);
          // const repliesSection = document.getElementById('replies-' + discussionId);


          // Toggle the visibility of the respective reply form
          if (replyForm) {
            if (replyForm.style.display === 'none' || !replyForm.style.display) {
              replyForm.style.display = 'block';
            } else {
              replyForm.style.display = 'none';
            }
          } else {
            console.error('Reply form not found for reply ID:', replyId);
          }

          //   if (replyForm.style.display === 'none' || !replyForm.style.display) {
          //     replyForm.style.display = 'block'; // Show reply form
          //     repliesSection.style.display = 'none'; // Hide replies
          //   } else {
          //     replyForm.style.display = 'none'; // Hide reply form
          //     repliesSection.style.display = 'block'; // Show replies
          //   }
          // } else {
          //   console.error('Reply form or replies section not found for discussion ID:', discussionId);
          // }
        }
      });
    });
  </script>

  <script>
    // Function to open the modal and load post content
    function openPostModal(discussionId) {
      fetch(`utility/fetch_replies.php?discussion_id=${discussionId}`)
        .then(response => response.text())
        .then(data => {
          document.getElementById('modal-body').innerHTML = data;
          document.getElementById('post-modal').style.display = 'block';
        })
        .catch(error => console.error('Error loading post:', error));
    }

    // Close the modal when the user clicks on <span> (x)
    document.querySelector('.close').addEventListener('click', function() {
      document.getElementById('post-modal').style.display = 'none';
    });

    // Close the modal when the user clicks outside of the modal
    window.addEventListener('click', function(event) {
      if (event.target === document.getElementById('post-modal')) {
        document.getElementById('post-modal').style.display = 'none';
      }
    });

    // Event listener for discussion title and view replies
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.discussion-title, .view-replies').forEach(function(element) {
        element.addEventListener('click', function(event) {
          event.preventDefault();
          const discussionId = this.getAttribute('data-post-id');
          openPostModal(discussionId);
        });
      });
    });
  </script>


  <script>
    // JavaScript for modal functionality
    const modal = document.getElementById('create-post-modal');
    const btn = document.getElementById('create-post-btn');
    const closeBtn = document.querySelector('#create-post-modal .close'); // Select the close button inside the modal

    btn.onclick = function() {
      modal.style.display = 'block';
    }

    // Close the modal when clicking the close button
    closeBtn.onclick = function() {
      modal.style.display = 'none';
    }


    window.onclick = function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    }

    // JavaScript for rich text editor
    // Function to wrap selected text in tags
    function formatText(tagOpen, tagClose) {
      var textArea = document.getElementById('editor'); // Assume you have a textarea or contenteditable div
      var selection = window.getSelection().toString();

      if (selection.length > 0) {
        document.execCommand('insertHTML', false, tagOpen + selection + tagClose);
      }
    }

    // Bold button
    document.getElementById('bold-btn').addEventListener('click', function() {
      document.execCommand('bold');
    });

    // Italic button
    document.getElementById('italic-btn').addEventListener('click', function() {
      document.execCommand('italic');
    });

    // Underline button
    document.getElementById('underline-btn').addEventListener('click', function() {
      document.execCommand('underline');
    });

    // Embed Image button
    document.getElementById('image-btn').addEventListener('click', function() {
      var imageUrl = prompt("Enter image URL");
      if (imageUrl) {
        document.execCommand('insertImage', false, imageUrl);
      }
    });

    // Insert Math button
    document.getElementById('math-btn').addEventListener('click', function() {
      var mathInput = prompt("Enter LaTeX math expression");
      if (mathInput) {
        formatText('$', '$'); // For LaTeX-style math embedding
      }
    });

    // Insert Link button
    document.getElementById('link-btn').addEventListener('click', function() {
      var url = prompt("Enter URL");
      if (url) {
        document.execCommand('createLink', false, url);
      }
    });

    // Bulleted List button
    document.getElementById('bullet-btn').addEventListener('click', function() {
      document.execCommand('insertUnorderedList');
    });

    // Codeblock button
    document.getElementById('codeblock-btn').addEventListener('click', function() {
      var codeLang = prompt("Enter programming language (optional)");
      var code = prompt("Enter code");
      if (code) {
        formatText('<pre><code' + (codeLang ? ' class="' + codeLang + '"' : '') + '>', '</code></pre>');
      }
    });

    // Subscript button
    document.getElementById('subscript-btn').addEventListener('click', function() {
      document.execCommand('subscript');
    });

    // Superscript button
    document.getElementById('superscript-btn').addEventListener('click', function() {
      document.execCommand('superscript');
    });
  </script>

  <?php include "../utility/footer.php"; ?>
  <!-- Script for js and chatbot -->
  <?php include "../utility/bot.php" ?>
  <script src="../js/script.js"></script>
  <!-- forum search functionality -->
  <script>
    // Function to handle the search functionality
    function performSearch() {
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
    }

    // Trigger search on button click
    document.getElementById('search-btn').addEventListener('click', performSearch);

    // Trigger search on pressing 'Enter' key in the input field
    document.getElementById('search').addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        performSearch(); // Perform search when "Enter" key is pressed
      }
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
  }

  .forum-search {
    display: flex;
    align-items: center;
    gap: 0.5em;
    margin-bottom: 1em;
    margin-right: 1em;
  }

  .forum-search input[type="text"] {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
  }

  .forum-search button {
    padding: 10px 20px;
    border: none;
    background-color: #5F9EA0;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
  }

  .forum-search button:hover {
    background-color: #0056b3;
  }

  @media (max-width: 375px) {
    #search-btn {
      display: none;
    }
  }

  .forum-create button,
  #submito {
    padding: 10px 20px;
    border: none;
    background-color: #5F9EA0;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
  }

  .forum-create button:hover {
    background-color: #5F9EA9;
  }

  .forum-filter {
    margin-top: 20px;
  }

  .forum-filter select {
    padding: 0.9em;
    border: 1px solid #ddd;
    border-radius: 0.5em;
  }

  .forum-post-actions,
  .r-forum-post-actions {
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
    padding: 0.5em;
    border: 1px solid #888;
    border-radius: 0.2em;
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
    width: 95%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  .editor-toolbar {
    margin-top: 0.5em;
  }

  .editor-toolbar button {
    background-color: #5F9EA0;
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
    width: 95%;
    min-height: 150px;
    max-height: 150px;
    padding: 0.5em;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow-y: scroll;
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
    border-radius: 0.2em;
    padding: 0.5em;
    margin-bottom: 1em;
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
    margin-bottom: 1em;
    margin-top: 1em;
    padding: 0.5em;
    border: 1px solid #dee2e6;
    border-radius: 0.2em;
    background-color: #f8f9fa;
  }

  .r-view-replies,
  .rr-view-replies,
  .view-replies {
    color: black;
  }

  body.dark-mode .r-view-replies,
  body.dark-mode .view-replies,
  body.dark-mode .rr-view-replies {
    color: #ccc;
  }

  .reply-actions {
    display: flex;
    margin-top: 10px;
    font-size: 14px;
    /* Adjust font size to control icon size */
  }

  .r-like-icon,
  .r-replies-icon,
  .like-icon,
  .replies-icon {
    display: inline-flex;
    align-items: center;
    margin-right: 20px;
    /* Space between icons */
    color: gray;
    /* Color for the icons */
    cursor: pointer;
    /* Cursor change on hover */
  }

  .r-like-icon i,
  .r-replies-icon i,
  .like-icon i,
  .replies-icon i {
    font-size: 18px;
    /* Size of the icons */
    margin-right: 5px;
    /* Space between icon and text */
  }

  /* Dark mode adjustments */
  body.dark-mode .r-like-icon,
  body.dark-mode.r-replies-icon,
  body.dark-mode .like-icon,
  body.dark-mode .replies-icon {
    color: #65787b;
    /* Color change for dark mode */
  }

  /* Dark mode */
  body.dark-mode .reply {
    background-color: #343a40;
    border-color: #454d55;
    color: #f8f9fa;
  }




  .replies-container {
    display: none;
    margin-top: 10px;
  }

  .replies-container.visible {
    display: block;
  }
</style>
<style>
  /* Modal background */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1000;
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

  /* Modal content */
  .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    /* Could be more or less, depending on screen size */
  }

  /* Close button */
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

  /* Styling for the reply form */
  .reply-form {
    margin-top: 1em;
    padding: 0.6em;
    border: 1px solid #ddd;
    border-radius: 0.2em;
    background-color: #f9f9f9;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .reply-form form {
    display: flex;
    flex-direction: column;
  }

  .reply-form textarea {
    width: 100%;
    height: 100px;
    padding: 0.5em;
    border: 1px solid #ddd;
    border-radius: 0.2em;
    margin-bottom: 0.5em;
    resize: vertical;
    font-size: 1em;
    color: #333;
    box-sizing: border-box;
  }

  .reply-form textarea::placeholder {
    color: #aaa;
  }

  .reply-form button {
    background-color: #5F9EA0;
    color: #fff;
    border: none;
    border-radius: 0.2em;
    padding: 0.5em 0.6em;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }

  .reply-form button:hover {
    background-color: #0056b3;
  }

  /* Dark Mode Styling for the reply form */
  body.dark-mode .reply-form {
    background-color: #333;
    border: 1px solid #444;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  }

  body.dark-mode .reply-form textarea {
    background-color: #444;
    border: 1px solid #555;
    color: #ddd;
    placeholder-color: #888;
  }

  body.dark-mode .reply-form textarea::placeholder {
    color: #888;
  }

  body.dark-mode .reply-form button {
    background-color: #0056b3;
    color: #fff;
    border: 1px solid #004494;
  }

  body.dark-mode .reply-form button:hover {
    background-color: #003d79;
  }

  /* Style for the settings icon */
  .settings-icon {
    cursor: pointer;
  }

  /* Style for the dropdown menu */
  .settings-menu {
    width: 100px;
    z-index: 1000;
  }

  .settings-menu ul {
    padding: 0;
  }

  .settings-menu li {
    padding: 10px;
    text-align: left;
  }

  .settings-menu li:hover {
    background-color: #f1f1f1;
  }
</style>