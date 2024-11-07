<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

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

    <!-- <div class="content">
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="error success">
                <h3>
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </h3>
            </div>
        <?php endif ?>

        <?php if (isset($_SESSION['username'])) : ?>
            <p style="font-size: 24px; color: #ffd700; text-align: center;  padding: 10px;  background: radial-gradient(circle, #5F9EA0, ); ">Name:<strong style=" color: #ffd700; font-size: 28px;">
                    <?php echo $_SESSION['username']; ?>
                </strong></p>
        <?php endif ?>
    </div> -->
    <!-- prev and next video -->
    <div class="pagination-icons">
        <a href="#"><i class="fas fa-chevron-left"></i>Prev</a>
        <a href="#">Next<i class="fas fa-chevron-right"></i></a>
    </div>

    <!-- Menu bar to toggle the video -->
    <div class="menu-bar">
        <a href="#" id="toggle-videos"><i class="fas fa-bars"></i> Menu</a>
    </div>

    <!-- --------------------------------------------------------------------------------------- -->
    <!-- Course material section starts -->
    <?php
    // Ensure course_id is passed
    if (!isset($_GET['course_id'])) {
        echo "Invalid course selection.";
        exit;
    }

    $courseId = intval($_GET['course_id']);

    //Database connection
    include "../utility/db.php";

    // Fetch course details
    $query = "SELECT course_title,course_description FROM courses WHERE course_id = $courseId";
    $courseResult = mysqli_query($conn, $query);
    $course = mysqli_fetch_assoc($courseResult);

    if (!$course) {
        echo "Course not found.";
        exit;
    }

    $courseTitle = htmlspecialchars($course['course_title']);
    // added new
    $courseDescription = $course['course_description'];
    //LIKE UNLIKE 
    $user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session
    $course_id = $_GET['course_id']; // Get the course ID from the URL

    // Check if the user has liked the course
    $liked = false;
    $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $liked = true;
    }
    // $stmt->close();
    // $conn->close();
    ?>

    <!-- Hidden div to hold user_id -->
    <div id="user-info" data-user-id="<?php echo $_SESSION['user_id']; ?>"></div>

    <div class="course_material_container">
        <div class="left-panel">
            <div class="inner-left-panel">
                <h3 class="course-title"><?php echo $courseTitle; ?></h3>
                <div>
                    <div>
                        <p id="course-description">
                            <!-- <i class='fas fa-info-circle'></i> -->
                            <span id="description-label">Course Description:</span>
                            <button id="toggle-description" onclick="toggleDescription()">Show More</button>
                        </p>
                    </div>
                    <div class="full-description" id="full-description" style="display:none;">
                        <span><?php echo $courseDescription; ?></span>
                    </div>
                </div>
                <!-- course objectives section coming... -->
                <ul>
                    <?php
                    // Fetch videos related to the selected course
                    $videoQuery = "SELECT video_title FROM videos WHERE course_id = $courseId";
                    $videoResult = mysqli_query($conn, $videoQuery);

                    if (mysqli_num_rows($videoResult) > 0) {
                        while ($video = mysqli_fetch_assoc($videoResult)) {
                            $videoTitle = htmlspecialchars($video['video_title']);
                            $videoPath = "../teacher/courses/$courseTitle/$videoTitle";
                            echo "<li>
                                <a href='#' class='video-link' data-videopath='$videoPath'>
                                    <i class='fas fa-play-circle'></i> <!-- Font Awesome video icon -->
                                    $videoTitle
                                </a>
                            </li>";
                        }
                    } else {
                        echo "<p>No videos available for this course.</p>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="video-player">
                <video id="video-player" controls>
                    <source id="video-source" src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <!-- content tabs -->
            <div class="content-tabs">
                <button class="nav-button" data-content="TRANSCRIPT">
                    <span>Transcript</span>
                </button>
                <button class="nav-button" data-content="MATERIALS">
                    <span>Materials</span>
                </button>

                <button class="nav-button" data-content="NOTES">
                    <span>Notes</span>
                </button>
                <button class="nav-button" data-content="DISCUSS">
                    <span>Discuss</span>
                </button>
            </div>

            <!-- Reference section -->
            <div class="display-section"></div>
            <div class="feedback">
                <!-- like/unlike toggle button -->
                <span id="like-icon" class="like-icon
                    <?php echo $liked ? 'liked' : ''; ?>">
                    <i class="fa fa-thumbs-up"></i>
                </span>
                <!-- Share button -->
                <span id="share-icon" class="share-icon">
                    <i class="fa fa-share-alt"></i> <!-- Share icon -->
                </span>

                <!-- Report button -->
                <span id="report-icon" class="report-icon">
                    <i class="fa fa-flag"></i> <!-- Report (flag) icon -->
                </span>
            </div>
        </div>
    </div>

    <!-- --------------------------------------------------------------------------------------- -->
    <script>
        // Retrieve the user ID from the hidden div's data attribute
        var USER_ID = $('#user-info').data('user-id');
        $(document).ready(function() {
            // Add a new note
            $(document).on('click', '.add-new-note', function() {
                var newNoteText = $('#new-note-text').val(); // Use ID to retrieve value
                console.log("NEW NOTE TEXT:", newNoteText);
                if (newNoteText.trim() === '') {
                    alert('Note cannot be empty');
                    return;
                }

                $.ajax({
                    url: 'utility/manageNotes.php',
                    type: 'POST',
                    data: {
                        action: 'add',
                        course_id: <?php echo $courseId; ?>, // Ensure courseId is defined correctly
                        note_text: newNoteText,
                        user_id: USER_ID,
                        video_title: lastSelectedVideoTitle
                    },
                    success: function(response) {
                        // alert('Note added successfully');

                        // Get course_id and course_title from URL
                        var urlParams = new URLSearchParams(window.location.search);
                        var course_id = urlParams.get('course_id');
                        var course_title = "<?php echo addslashes($courseTitle); ?>"; // Output the course title in a safe way
                        // Get content tabs buttons
                        const buttons = document.querySelectorAll('.nav-button');
                        const displaySection = document.querySelector('.display-section');
                        $.ajax({
                            url: 'utility/getContent.php',
                            type: 'GET',
                            data: {
                                content_type: 'notes-display-only',
                                course_id: <?php echo $courseId; ?>,
                                course_title: course_title,
                                user_id: USER_ID,
                                video_title: lastSelectedVideoTitle
                            },
                            success: function(response) {
                                // Update the display section with the fetched content
                                displaySection.innerHTML = response;
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading content:', error);
                                displaySection.innerHTML = 'Error loading content';
                            }
                        });


                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText); // Improved error logging
                    }
                });
            });

            // Edit and save existing notes
            $(document).on('click', '.save-note', function() {
                var noteItem = $(this).closest('.note-item');
                var noteId = noteItem.data('note-id');
                var noteText = noteItem.find('.note-text').val();
                console.log("NOTE ITEM:", noteItem);
                console.log("NOTE ID:", noteId);
                console.log("EDITED NOTE TEXT:", noteText);

                $.ajax({
                    url: 'utility/manageNotes.php',
                    type: 'POST',
                    data: {
                        action: 'edit',
                        note_id: noteId,
                        note_text: noteText,
                        user_id: USER_ID,
                        course_id: <?php echo $courseId; ?>, // Ensure course_id is defined correctly
                        video_title: lastSelectedVideoTitle
                    },
                    success: function(response) {
                        alert('Note updated successfully -product.php');

                        // Get course_id and course_title from URL
                        var urlParams = new URLSearchParams(window.location.search);
                        var course_id = urlParams.get('course_id');
                        var course_title = "<?php echo addslashes($courseTitle); ?>"; // Output the course title in a safe way
                        // Get content tabs buttons
                        const buttons = document.querySelectorAll('.nav-button');
                        const displaySection = document.querySelector('.display-section');
                        $.ajax({
                            url: 'utility/getContent.php',
                            type: 'GET',
                            data: {
                                content_type: 'notes-display-only',
                                course_id: <?php echo $courseId; ?>,
                                course_title: course_title,
                                user_id: USER_ID,
                                video_title: lastSelectedVideoTitle
                            },
                            success: function(response) {
                                // Update the display section with the fetched content
                                displaySection.innerHTML = response;
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading content:', error);
                                displaySection.innerHTML = 'Error loading content';
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

            // Delete a note
            $(document).on('click', '.delete-note', function() {
                var noteItem = $(this).closest('.note-item');
                var noteId = noteItem.data('note-id');
                console.log("DELETE NOTE:");
                console.log("NOTE ITEM:", noteItem);
                console.log("NOTE ID:", noteId);
                console.log("USER_ID:", USER_ID);
                $.ajax({
                    url: 'utility/manageNotes.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        note_id: noteId,
                        course_id: <?php echo $courseId; ?>,
                        user_id: USER_ID,
                        video_title: lastSelectedVideoTitle,

                    },
                    success: function(response) {
                        alert('Note deleted successfully -product.php');
                        // noteItem.remove();
                        // Get course_id and course_title from URL
                        var urlParams = new URLSearchParams(window.location.search);
                        var course_id = urlParams.get('course_id');
                        var course_title = "<?php echo addslashes($courseTitle); ?>"; // Output the course title in a safe way
                        // Get content tabs buttons
                        const buttons = document.querySelectorAll('.nav-button');
                        const displaySection = document.querySelector('.display-section');
                        $.ajax({
                            url: 'utility/getContent.php',
                            type: 'GET',
                            data: {
                                content_type: 'notes-display-only',
                                course_id: <?php echo $courseId; ?>,
                                course_title: course_title,
                                user_id: USER_ID,
                                video_title: lastSelectedVideoTitle
                            },
                            success: function(response) {
                                // Update the display section with the fetched content
                                displaySection.innerHTML = response;
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading content:', error);
                                displaySection.innerHTML = 'Error loading content';
                            }
                        });
                    },

                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

        });
    </script>

</body>

<!-- AJAX for loading materials data (pdfs and docx) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Get course_id and course_title from URL
        var urlParams = new URLSearchParams(window.location.search);
        var course_id = urlParams.get('course_id');
        var course_title = "<?php echo addslashes($courseTitle); ?>"; // Output the course title in a safe way

        // Retrieve the user ID from the hidden div's data attribute
        var USER_ID = $('#user-info').data('user-id');

        // Get content tabs buttons
        const buttons = document.querySelectorAll('.nav-button');
        const displaySection = document.querySelector('.display-section');

        // Add event listeners to all buttons
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const contentType = button.getAttribute('data-content').toLowerCase();

                // Skip the AJAX call if the button is "Transcript"
                if (contentType === 'transcript') {
                    // Optionally, you can handle the transcript loading separately here.
                    return; // Skip the AJAX call for Transcript button
                }

                // Make an AJAX request to get the content for other buttons
                $.ajax({
                    url: 'utility/getContent.php',
                    type: 'GET',
                    data: {
                        content_type: contentType,
                        course_id: course_id,
                        course_title: course_title,
                        user_id: USER_ID,
                        video_title: lastSelectedVideoTitle
                    },
                    success: function(response) {
                        // Update the display section with the fetched content
                        displaySection.innerHTML = response;
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading content:', error);
                        displaySection.innerHTML = 'Error loading content';
                    }
                });
            });
        });
    });
</script>

<!-- JavaScript for dynamic video loading -->
<script>
    // Cache variable to store the last selected video title
    var lastSelectedVideoTitle = "";

    // Function to load transcript based on video title
    function loadTranscript(videoTitle) {
        $.ajax({
            url: 'utility/getTranscript.php', // Assume you have this file to handle transcript requests
            type: 'GET',
            data: {
                video_title: videoTitle, // Send video title to get the transcript
                course_id: <?php echo $courseId; ?> // Send course ID if needed
            }, // Pass the video title to get the transcript
            success: function(response) {
                // Update the display-section with the fetched transcript
                document.querySelector('.display-section').innerHTML = response;
            },
            error: function(xhr, status, error) {
                console.error('Error loading transcript:', error);
                document.querySelector('.display-section').innerHTML = 'Error loading transcript';
            }
        });
    }

    // Function to manage note based on video title
    function manageNotes(videoTitle) {
        $.ajax({
            url: 'utility/manageNotes.php',
            type: 'POST',
            data: {
                user_id: $_SESSION['user_id'],
                course_id: <?php echo $courseId; ?>,

            }
        });
    }

    // Event listener for video links to update cache and load transcript
    document.querySelectorAll('.video-link').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var videoPath = this.getAttribute('data-videopath');
            var videoPlayer = document.getElementById('video-player');
            var videoSource = document.getElementById('video-source');

            // Update video player with new source
            videoSource.src = videoPath;
            videoPlayer.load();
            videoPlayer.play(); // Optional: Auto-play the video

            // Cache the video title for transcript purposes
            lastSelectedVideoTitle = this.textContent.trim();

            // Load the transcript for the selected video
            loadTranscript(lastSelectedVideoTitle);
        });
    });

    // Event listener for "Transcript" button in content-tabs
    document.querySelector('.nav-button[data-content="TRANSCRIPT"]').addEventListener('click', function() {
        // Check if there is a cached video title
        if (lastSelectedVideoTitle) {
            // Load the transcript for the cached video
            loadTranscript(lastSelectedVideoTitle);
        } else {
            // If no video has been selected, show a message
            document.querySelector('.display-section').innerHTML = 'Please select a video to view its transcript.';
        }
    });
</script>

<script>
    // like & unlike script code
    $(document).ready(function() {
        // Get course_id from URL
        var urlParams = new URLSearchParams(window.location.search);
        var course_id = urlParams.get('course_id');

        // Get user_id from PHP session
        var user_id = <?php echo $_SESSION['user_id']; ?>;
        $('#like-icon').on('click', function() {
            var $icon = $(this);
            var liked = $icon.hasClass('liked');
            // Toggle the liked class
            $icon.toggleClass('liked');
            // Send AJAX request
            $.ajax({
                url: 'utility/like.php',
                type: 'POST',
                data: {
                    user_id: user_id,
                    course_id: course_id,
                    liked: !liked
                },
                success: function(response) {
                    console.log('Like status updated:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating like status:', error);
                }
            });
        });
    });
    //Description expand and reduce effect
    function toggleDescription() {
        var description = document.getElementById('full-description');
        var button = document.getElementById('toggle-description');

        if (description.style.display === 'none') {
            description.style.display = 'inline';
            button.textContent = 'Show Less';
        } else {
            description.style.display = 'none';
            button.textContent = 'Show More';
        }
    }
</script>
<!-- HANDLE NOTE ACTIONS -->
<script>
    document.getElementById('toggle-videos').addEventListener('click', function() {
        const menuBar = document.querySelector('.menu-bar');
        menuBar.classList.toggle('fixed'); // Toggle the position class
    });
    document.getElementById('toggle-videos').addEventListener('click', function() {
        const leftPanel = document.querySelector('.left-panel');
        if (leftPanel.style.display === 'none' || leftPanel.style.display === '') {
            leftPanel.style.display = 'block';
        } else {
            leftPanel.style.display = 'none';
        }
    });
    const videoLinks = document.querySelectorAll('.video-link');
    videoLinks.forEach(link => {
        link.addEventListener('click', function() {
            const leftPanel = document.querySelector('.left-panel');
            const menuBar = document.querySelector('.menu-bar');

            if (window.innerWidth <= 375) {
                leftPanel.style.display = 'none'; // Hide the left panel
                menuBar.classList.remove('fixed'); // Reset the menu bar to the original position
            }
        });
    });
</script>
<!-- Footer -->
<!-- <?php include "../utility/footer.php"; ?> -->
<!-- Script for js and chatbot -->
<?php include "../utility/bot.php" ?>

<script src="../js/script.js"></script>
</body>

</html>

<?php $conn->close(); ?>

<style>
    /* Hide the icons by default using the class 'pagination-icons' */
    .pagination-icons a {
        display: none;

    }

    /* Hide the menu bar by default */
    .menu-bar {
        display: none;
        background-color: #333;
        color: white;
        padding: 10px;
        text-align: center;
    }

    .menu-bar a {
        color: white;
        text-decoration: none;
        font-size: 18px;
    }

    .menu-bar a:hover {
        text-decoration: none;
    }


    /* Display the icons when the screen size is 375px or smaller */
    @media (max-width: 375px) {
        .pagination-icons a {
            display: inline-block;
            font-size: 1em;
            padding: 0.5em;
            text-decoration: none;
            color: black;
        }

        .pagination-icons i {
            font-size: 1em;
        }

        .menu-bar {
            display: block;
        }

        .menu-bar.fixed {
            position: fixed;
            top: 8px;
            left: 8px;
            z-index: 2000;
        }

        .left-panel {
            display: none;
            position: fixed;
            z-index: 1000;
            background-color: white;
            width: 100%;
            /* adjust as needed */
            height: 100%;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
            overflow-y: scroll;
        }

        .inner-left-panel {
            position: relative;
            top: 14px;
        }

        .right-panel {
            width: 100%;
        }
    }

    /* By default, the video container is visible */
    .video-container {
        display: block;
    }


    .inner-left-panel {
        padding: 5px;
        height: 100%;
        /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); */
        /* border-radius: 10px; */
    }

    #user-info {
        display: none;
    }

    /* transcript content style with display-content */
    .transcript-content {
        background-color: #f9f9f9;
        /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
        /* margin-top: 15px; */
        /* padding: 20px; */
        /* border-radius: 6px; */
        /* border: 1px solid #ddd; */
        max-height: 100%;
        overflow-y: auto;
        line-height: 1.8;
        font-size: 0.9rem;
        color: #333;
    }

    .transcript-content p {
        margin-bottom: 10px;
    }

    /* Optional for dark mode support */
    .dark-mode .transcript-content {
        background-color: #444;
        border: 1px solid #555;
        color: #ddd;
    }


    /* notes style within display-content */
    .note-item {
        background-color: #f9f9f9;
        padding: 0.5rem;
        margin-bottom: 0.5em;
        border-radius: 0.5rem;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .note-text {
        width: 90%;
        padding: 1em;
        font-size: 0.9em;
        border: 1px solid #ccc;
        border-radius: 0.5em;
        resize: vertical;
        margin-bottom: 0.09em;
    }

    button.save-note,
    button.delete-note {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    button.delete-note {
        background-color: #dc3545;
    }

    button.save-note:hover,
    button.delete-note:hover {
        opacity: 0.9;
    }

    #new-note-text {
        width: 90%;
        padding: 1em;
        font-size: 0.9em;
        border: 1px solid #ccc;
        border-radius: 0.5em;
        resize: vertical;
        margin-top: 0.09em;
    }

    button.add-new-note {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    button.add-new-note:hover {
        opacity: 0.9;
    }

    /* Dark Mode */
    .dark-mode .display-section {
        background-color: #333;
        border: 1px solid #444;
        color: #ddd;
    }

    .dark-mode .note-item {
        background-color: #444;
        border: 1px solid #555;
    }

    .dark-mode .note-text,
    .dark-mode #new-note-text {
        background-color: #555;
        color: #ddd;
        border: 1px solid #666;
    }

    .dark-mode button.save-note,
    .dark-mode button.delete-note,
    .dark-mode button.add-new-note {
        background-color: #0056b3;
        color: white;
    }

    .dark-mode button.delete-note {
        background-color: #c82333;
    }

    .dark-mode button.add-new-note {
        background-color: #218838;
    }

    .dark-mode button:hover {
        opacity: 0.85;
    }

    /* discuss style */
    /* Light Mode */
    body.light-mode a {
        color: black;
        /* Light mode link color */
    }

    body.light-mode a i {
        color: black;
        /* Light mode icon color */
    }

    /* Dark Mode */
    body.dark-mode a {
        color: white;
        /* Dark mode link color */
    }

    body.dark-mode a i {
        color: white;
        /* Dark mode icon color */
    }
</style>