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
  // Handle video upload
  if (!empty($_FILES['course_videos']['name'][0])) {
    foreach ($_FILES['course_videos']['name'] as $key => $videoFileName) {
      $videoFilePath = $courseDir . '/' . basename($videoFileName);

      if (move_uploaded_file($_FILES['course_videos']['tmp_name'][$key], $videoFilePath)) {
        $insertVideoQuery = "INSERT INTO videos (course_id, video_title) VALUES ($courseId, '$videoFileName')";
        mysqli_query($conn, $insertVideoQuery);
        $videoUploadSuccess = "Videos uploaded successfully.";
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


  <!-- New Course creation form -->
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
        <div>
          <label for="course_videos">Upload Course Videos:</label>
          <input type="file" id="course_videos" name="course_videos[]" accept="video/*" multiple required>
        </div>
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
  //toggle button to display and create new course form ---- for teacher's interface
  document.getElementById("toggle-course-form").addEventListener("click", function() {
    const courseForm = document.querySelector(".course-form-home");
    if (courseForm.style.display === "none" || courseForm.style.display === "") {
      courseForm.style.display = "block";
    } else {
      courseForm.style.display = "none";
    }
  });
</script>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>