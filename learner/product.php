<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

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

    <!-- copied from demo start -->
    <div class="content">
        <!-- notification message -->
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

        <!-- logged in user information -->
        <?php if (isset($_SESSION['username'])) : ?>
            <p style="font-size: 24px; color: #ffd700; text-align: center;  padding: 10px;  background: linear-gradient(135deg, #5F9EA0, black); box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);">Name:<strong style="color: #ffd700; font-size: 28px;">
                    <?php echo $_SESSION['username']; ?>
                </strong></p>
        <?php endif ?>
    </div>
    <!-- copied from demo ends -->


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



    <div class="course_material_container">
        <div class="left-panel">
            <h3 class="course-title"><?php echo $courseTitle; ?></h3>

            <p id="course-description">
                <i class='fas fa-info-circle'></i> <!-- Font Awesome info circle icon -->
                <span id="description-label">Course Description:</span>
                <span id="full-description" style="display:none;"><?php echo $courseDescription; ?></span>
                <button id="toggle-description" onclick="toggleDescription()">Show More</button>
            </p>

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
            <div class="display-section">

            </div>
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

</body>


<style>
    .feedback {
        display: flex;
        justify-content: flex-start;
        border: solid 1px;
        padding: 2px;
        margin-top: 2px;
        align-items: center;
        gap: 25px;

    }

    .feedback i {
        font-size: 24px;
    }

    .feedback i:hover {
        color: #d3d3d3;
    }

    .feedback span {
        cursor: pointer;
    }

    .display-section {
        padding: 30px;
        border: 1px solid gray;
        height: 45%;
        overflow-y: auto;
        line-height: 2;
    }

    body.dark-mode .display-section {
        border: 1px solid white;
    }

    .nav-button {
        position: relative;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        margin-right: 10px;
        overflow: hidden;
    }

    .nav-button span {
        position: relative;
        z-index: 1;
    }

    .nav-button::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 2px;
        background-color: #ff4500;
        transform: scaleX(0);
        transform-origin: center;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .nav-button:hover::after,
    .nav-button:focus::after {
        transform: scaleX(1);
        background-color: #5F9EA0;
    }

    body.dark-mode .nav-button span {
        color: white;
    }
</style>

<!-- AJAX for loading materials data (pdfs and docx) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Get course_id and course_title from URL
        var urlParams = new URLSearchParams(window.location.search);
        var course_id = urlParams.get('course_id');
        var course_title = "<?php echo addslashes($courseTitle); ?>"; // Output the course title in a safe way

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
                        course_title: course_title
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

<!-- Footer -->
<?php include "../utility/footer.php"; ?>
<!-- Script for js and chatbot -->
<?php include "../utility/bot.php" ?>

<script src="../js/script.js"></script>
</body>

</html>

<?php $conn->close(); ?>