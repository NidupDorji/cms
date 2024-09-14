<?php
// Enable Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Authentication check using auth.php
include "../utility/auth.php";

// Database connection
include "../utility/db.php";

// Handling course creation and file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $courseName = mysqli_real_escape_string($conn, $_POST['course_name']);
  $courseDesc = mysqli_real_escape_string($conn, $_POST['course_description']);
  $createdBy = $_SESSION['user_id']; // Assuming user_id is stored in the session after login

  // Prepare thumbnail upload
  $thumbnailFileName = '';
  if (!empty($_FILES['thumbnail']['name'])) {
    $thumbnailFileName = basename($_FILES['thumbnail']['name']);
    $thumbnailDir = 'courses/' . $courseName . '/thumbnail/';
    $thumbnailFilePath = $thumbnailDir . $thumbnailFileName;

    // Create the thumbnail directory if it doesn't exist
    if (!file_exists($thumbnailDir)) {
      mkdir($thumbnailDir, 0777, true);
    }
  }

  // Insert course details into the courses table
  $insertCourseQuery = "INSERT INTO courses (course_title, course_description, created_by, thumbnail_path) VALUES ('$courseName', '$courseDesc', $createdBy, '$thumbnailFileName')";
  if (mysqli_query($conn, $insertCourseQuery)) {
    $courseId = mysqli_insert_id($conn); // Get the course_id of the newly inserted course

    // Create course directory if it doesn't exist
    $courseDir = 'courses/' . $courseName;
    if (!file_exists($courseDir)) {
      mkdir($courseDir, 0777, true);
    }

    // Handle thumbnail upload
    if (!empty($_FILES['thumbnail']['name'])) {
      if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailFilePath)) {
        $thumbnailUploadSuccess = "Thumbnail uploaded successfully.";
      } else {
        $thumbnailUploadError = "Failed to upload thumbnail.";
      }
    }

    $courseCreationSuccess = "Course created successfully.";
  } else {
    $courseCreationError = "Error: " . $insertCourseQuery . "<br>" . mysqli_error($conn);
  }

  // Handle PDF/DOCX uploads
  if (!empty($_FILES['reference_files']['name'])) {
    foreach ($_FILES['reference_files']['name'] as $key => $fileName) {
      $filePath = $courseDir . '/' . basename($fileName);

      if (move_uploaded_file($_FILES['reference_files']['tmp_name'][$key], $filePath)) {
        // Insert reference material into the materials table
        $insertMaterialQuery = "INSERT INTO materials (course_id, material_title) VALUES ($courseId, '$fileName')";
        mysqli_query($conn, $insertMaterialQuery);
      } else {
        $fileUploadError = "Failed to upload $fileName.";
      }
    }
  }

  // Handle video and transcript uploads
  if (!empty($_FILES['course_videos']['name'][0])) {
    foreach ($_FILES['course_videos']['name'] as $key => $videoFileName) {
      $videoFilePath = $courseDir . '/' . basename($videoFileName);
      $transcript = mysqli_real_escape_string($conn, $_POST['transcript'][$key]); // Handle transcript input

      if (move_uploaded_file($_FILES['course_videos']['tmp_name'][$key], $videoFilePath)) {
        // Insert video and transcript into the videos table
        $insertVideoQuery = "INSERT INTO videos (course_id, video_title, transcript) VALUES ($courseId, '$videoFileName', '$transcript')";
        mysqli_query($conn, $insertVideoQuery);
        $videoUploadSuccess = "Videos and transcripts uploaded successfully.";
      } else {
        $videoUploadError = "Failed to upload video: $videoFileName.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- head using head.php -->
<?php include "utility/head.php" ?>

<body>
  <?php include "utility/header.php" ?>

  <!-- welcome msg using welcome.php -->
  <?php include "../utility/welcome.php" ?>

  <!-- my courses -->
  <?php include "utility/my_courses.php" ?>

  <hr class="stylish-divider"> <!-- Stylish divider -->

  <!-- Button to toggle the visibility of the course form -->
  <button id="toggle-course-form" class="toggle-button">Create New Course</button>

  <!-- New Course creation form -->
  <div class="container">
    <?php if (isset($courseCreationSuccess)) : ?>
      <p style="color: green;"><?php echo $courseCreationSuccess; ?></p>
    <?php elseif (isset($courseCreationError)) : ?>
      <p style="color: red;"><?php echo $courseCreationError; ?></p>
    <?php endif; ?>
    <div class="course-form-home">
      <h1>Create a New Course</h1>
      <form action="home.php" method="POST" enctype="multipart/form-data">
        <div>
          <label for="course_name">Course Name:</label>
          <input type="text" id="course_name" name="course_name" required>
        </div>
        <div>
          <label for="course_description">Course Description:</label>
          <textarea id="course_description" name="course_description" required></textarea>
        </div>
        <div>
          <label for="thumbnail">Upload Course Thumbnail:</label>
          <input type="file" id="thumbnail" name="thumbnail" accept="image/*" required>
        </div>

        <div>
          <label for="reference_files">Upload Reference Files (PDF/DOCX):</label>
          <input type="file" id="reference_files" name="reference_files[]" accept=".pdf,.docx" multiple required>
        </div>

        <div id="video-section">
          <div class="video-transcript-group">
            <div class="video-upload">
              <label for="course_videos">Upload Course Video:</label>
              <input type="file" id="course_videos" name="course_videos[]" accept="video/*" required>
            </div>
            <div class="transcript-upload">
              <label for="transcript">Transcript:</label>
              <textarea id="transcript" name="transcript[]" required></textarea>
            </div>
            <button type="button" class="remove-video">-</button>
          </div>
        </div>

        <!-- Add more video upload sections -->
        <button type="button" id="add-video" class="toggle-button">+ Add Another Video</button>

        <div>
          <button type="submit">Create Course</button>
        </div>
      </form>
    </div>
  </div>

  <!-- footer -->
  <?php include "../utility/footer.php"; ?>
  <!-- bot script -->
  <?php include "../utility/bot.php" ?>
  <script src="../js/script.js"></script>
</body>

<script>
  // Toggle button to display and create new course form ---- for teacher's interface
  document.getElementById("toggle-course-form").addEventListener("click", function() {
    const courseForm = document.querySelector(".course-form-home");
    if (courseForm.style.display === "none" || courseForm.style.display === "") {
      courseForm.style.display = "block";
    } else {
      courseForm.style.display = "none";
    }
  });

  // Add more video upload fields
  document.getElementById("add-video").addEventListener("click", function() {
    const videoSection = document.getElementById("video-section");

    // Create video and transcript container
    const videoTranscriptGroup = document.createElement("div");
    videoTranscriptGroup.classList.add("video-transcript-group");

    // Create video input element
    const videoUploadDiv = document.createElement("div");
    videoUploadDiv.classList.add("video-upload");
    const videoLabel = document.createElement("label");
    videoLabel.innerHTML = "Upload Course Video:";
    const videoInput = document.createElement("input");
    videoInput.type = "file";
    videoInput.name = "course_videos[]";
    videoInput.accept = "video/*";
    videoUploadDiv.appendChild(videoLabel);
    videoUploadDiv.appendChild(videoInput);

    // Create transcript textarea
    const transcriptUploadDiv = document.createElement("div");
    transcriptUploadDiv.classList.add("transcript-upload");
    const transcriptLabel = document.createElement("label");
    transcriptLabel.innerHTML = "Transcript:";
    const transcriptInput = document.createElement("textarea");
    transcriptInput.name = "transcript[]";
    transcriptUploadDiv.appendChild(transcriptLabel);
    transcriptUploadDiv.appendChild(transcriptInput);

    // Create remove button
    const removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.classList.add("remove-video");
    removeButton.innerHTML = "-";

    // Append video, transcript, and remove button to the group
    videoTranscriptGroup.appendChild(videoUploadDiv);
    videoTranscriptGroup.appendChild(transcriptUploadDiv);
    videoTranscriptGroup.appendChild(removeButton);

    // Append group to the video section
    videoSection.appendChild(videoTranscriptGroup);

    // Remove video and transcript input when clicking the minus button
    removeButton.addEventListener("click", function() {
      videoTranscriptGroup.remove();
    });
  });

  // Handle initial remove button in case user removes the first group
  document.querySelectorAll(".remove-video").forEach(function(button) {
    button.addEventListener("click", function() {
      button.closest(".video-transcript-group").remove();
    });
  });
</script>

</html>
<?php
mysqli_close($conn)
?>