<?php
session_start();

// Database connection
include "../utility/db.php";


// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$courseId = $_GET['id']; // Get course ID from the URL
$userId = $_SESSION['user_id']; // Get the current logged-in user ID

// Initialize variables for course details
$courseTitle = '';
$courseDescription = '';
$thumbnailPath = '';
$errorMessage = '';
$successMessage = '';

// Retrieve the current course details
if ($stmt = $conn->prepare("SELECT course_title, course_description, thumbnail_path FROM courses WHERE course_id = ? AND created_by = ?")) {
  $stmt->bind_param("ii", $courseId, $userId);
  $stmt->execute();
  $stmt->bind_result($courseTitle, $courseDescription, $thumbnailPath);

  if (!$stmt->fetch()) {
    $errorMessage = "Course not found or you don't have permission to edit this course.";
  }
  $stmt->close();
} else {
  $errorMessage = "Error preparing statement: " . $conn->error;
}

// Handle form submission for updating the course
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $newTitle = $_POST['course_title'];
  $newDescription = $_POST['course_description'];

  // Update course details in the database
  if ($stmt = $conn->prepare("UPDATE courses SET course_title = ?, course_description = ? WHERE course_id = ? AND created_by = ?")) {
    $stmt->bind_param("ssii", $newTitle, $newDescription, $courseId, $userId);

    if ($stmt->execute()) {
      $successMessage = "Course updated successfully!";
      // Update the variables to display the new data in the form
      $courseTitle = $newTitle;
      $courseDescription = $newDescription;
    } else {
      $errorMessage = "Failed to update the course.";
    }
    $stmt->close();
  } else {
    $errorMessage = "Error preparing statement: " . $conn->error;
  }
}

// Handle file deletion
if (isset($_POST['delete_file'])) {
  $fileToDelete = $_POST['delete_file'];
  $isVideo = strpos($fileToDelete, '.mp4') !== false; // Adjust this to match video file extensions you support
  $isMaterial = !$isVideo; // Assuming if it's not a video, it's a material (PDF/DOCX)

  // Delete file from filesystem
  if (unlink($fileToDelete)) {
    $successMessage = "File deleted successfully!";

    // Update the database
    if ($isVideo) {
      // Remove video record from the database
      $videoTitle = basename($fileToDelete);
      $stmt = $conn->prepare("DELETE FROM videos WHERE course_id = ? AND video_title = ?");
      $stmt->bind_param("is", $courseId, $videoTitle);
      $stmt->execute();
      $stmt->close();
    } elseif ($isMaterial) {
      // Remove material record from the database
      $materialTitle = basename($fileToDelete);
      $stmt = $conn->prepare("DELETE FROM materials WHERE course_id = ? AND material_title = ?");
      $stmt->bind_param("is", $courseId, $materialTitle);
      $stmt->execute();
      $stmt->close();
    }
  } else {
    $errorMessage = "Failed to delete file.";
  }
}


// Handle new file uploads (thumbnail, videos, documents)
if (isset($_FILES['new_thumbnail']) || isset($_FILES['new_videos']) || isset($_FILES['new_documents'])) {
  $courseDir = 'courses/' . $courseTitle;
  $thumbnailDir = $courseDir . '/thumbnail/';

  // Ensure the directory exists
  if (!is_dir($thumbnailDir)) {
    mkdir($thumbnailDir, 0777, true);
  }
  // Update thumbnail
  if (!empty($_FILES['new_thumbnail']['name'])) {
    $newThumbnailName = basename($_FILES['new_thumbnail']['name']);
    $newThumbnailPath = $thumbnailDir . $newThumbnailName;

    // Check if there's an existing thumbnail and delete it
    if (!empty($thumbnailPath) && file_exists($thumbnailDir . $thumbnailPath)) {
      unlink($thumbnailDir . $thumbnailPath); // Delete the previous thumbnail
    }

    if (move_uploaded_file($_FILES['new_thumbnail']['tmp_name'], $newThumbnailPath)) {
      // Update the thumbnail path in the database
      $stmt = $conn->prepare("UPDATE courses SET thumbnail_path = ? WHERE course_id = ?");
      $stmt->bind_param("si", $newThumbnailName, $courseId);
      $stmt->execute();
      $stmt->close();

      // Update the variable to the new thumbnail path
      $thumbnailPath = $newThumbnailName;
      $successMessage = "Thumbnail updated successfully!";
    } else {
      $errorMessage = "Failed to upload new thumbnail.";
    }
  }



  // Handle new video uploads
  if (!empty($_FILES['new_videos']['name'][0])) {
    foreach ($_FILES['new_videos']['name'] as $key => $videoFileName) {
      $videoFilePath = $courseDir . '/' . basename($videoFileName);
      if (move_uploaded_file($_FILES['new_videos']['tmp_name'][$key], $videoFilePath)) {
        $stmt = $conn->prepare("INSERT INTO videos (course_id, video_title) VALUES (?, ?)");
        $stmt->bind_param("is", $courseId, $videoFileName);
        $stmt->execute();
        $stmt->close();
      }
    }
    $successMessage = "New videos uploaded successfully!";
  }

  // Handle new PDF/DOCX uploads
  if (!empty($_FILES['new_documents']['name'][0])) {
    foreach ($_FILES['new_documents']['name'] as $key => $docFileName) {
      $docFilePath = $courseDir . '/' . basename($docFileName);
      if (move_uploaded_file($_FILES['new_documents']['tmp_name'][$key], $docFilePath)) {
        $stmt = $conn->prepare("INSERT INTO materials (course_id, material_title) VALUES (?, ?)");
        $stmt->bind_param("is", $courseId, $docFileName);
        $stmt->execute();
        $stmt->close();
      }
    }
    $successMessage = "New documents uploaded successfully!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include "utility/head.php" ?>

<body>
  <?php include "utility/header.php" ?>
  <!-- welcome msg using welcome.php -->
  <?php include "../utility/welcome.php" ?>



  <div class="container">
    <div class="course-form">
      <h1>Edit Course</h1>

      <?php if (!empty($errorMessage)) : ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
      <?php endif; ?>

      <?php if (!empty($successMessage)) : ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
      <?php endif; ?>

      <form action="edit_course.php?id=<?php echo htmlspecialchars($courseId); ?>" method="POST" enctype="multipart/form-data">
        <div>
          <label for="course_title">Course Title:</label>
          <input type="text" id="course_title" name="course_title" value="<?php echo htmlspecialchars($courseTitle); ?>" required>
        </div>
        <div>
          <label for="course_description">Course Description:</label>
          <textarea id="course_description" name="course_description" required><?php echo htmlspecialchars($courseDescription); ?></textarea>
        </div>

        <!-- Display existing thumbnail -->
        <div>
          <label>Current Thumbnail:</label>
          <img src="courses/<?php echo $courseTitle; ?>/thumbnail/<?php echo htmlspecialchars($thumbnailPath); ?>" alt="Course Thumbnail" width="150">
        </div>
        <div>
          <label for="new_thumbnail">Upload New Thumbnail:</label>
          <input type="file" id="new_thumbnail" name="new_thumbnail" accept="image/*">
        </div>

        <!-- Display existing videos -->
        <div>
          <label>Current Videos:</label>
          <?php
          $videoQuery = $conn->query("SELECT video_title FROM videos WHERE course_id = $courseId");
          while ($video = $videoQuery->fetch_assoc()) {
            echo '<p>' . htmlspecialchars($video['video_title']) . ' <button type="submit" name="delete_file" value="courses/' . $courseTitle . '/' . htmlspecialchars($video['video_title']) . '">Delete</button></p>';
          }
          ?>
        </div>
        <div>
          <label for="new_videos">Upload New Videos:</label>
          <input type="file" id="new_videos" name="new_videos[]" accept="video/*" multiple>
        </div>

        <!-- Display existing documents -->
        <div>
          <label>Current Documents (PDF/DOCX):</label>
          <?php
          $docQuery = $conn->query("SELECT material_title FROM materials WHERE course_id = $courseId");
          while ($doc = $docQuery->fetch_assoc()) {
            echo '<p>' . htmlspecialchars($doc['material_title']) . ' <button type="submit" name="delete_file" value="courses/' . $courseTitle . '/' . htmlspecialchars($doc['material_title']) . '">Delete</button></p>';
          }
          ?>
        </div>
        <div>
          <label for="new_documents">Upload New Documents (PDF/DOCX):</label>
          <input type="file" id="new_documents" name="new_documents[]" accept=".pdf,.docx" multiple>
        </div>

        <div>
          <button type="submit">Update Course</button>
        </div>
      </form>

      <a href="home.php">Back to My Courses</a> <!-- Link back to the courses page -->
    </div>
  </div>
  <!-- footer -->
  <?php include "../utility/footer.php"; ?>
  <!-- bot script -->
  <?php include "../utility/bot.php" ?>
  <script src="../js/script.js"></script>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>