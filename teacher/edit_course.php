<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
  $oldCourseDir = 'courses/' . $courseTitle;
  $newCourseDir = 'courses/' . $newTitle;

  // Check if the title has changed
  if ($newTitle !== $courseTitle) {
    // Rename the course directory
    if (is_dir($oldCourseDir)) {
      if (rename($oldCourseDir, $newCourseDir)) {
        $courseTitle = $newTitle; // Update the $courseTitle variable
      } else {
        $errorMessage = "Failed to rename the course directory.";
      }
    }
  }

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

  // Handle file deletion
  if (isset($_POST['delete_file'])) {
    $fileToDelete = $_POST['delete_file'];
    $isVideo = strpos($fileToDelete, '.mp4') !== false;
    $isMaterial = !$isVideo;

    // Delete file from filesystem
    if (file_exists($fileToDelete) && unlink($fileToDelete)) {
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
      unlink($thumbnailDir . $thumbnailPath);
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

  // Handle existing video edits
  if (isset($_POST['existing_video_transcripts'])) {
    foreach ($_POST['existing_video_transcripts'] as $videoId => $transcript) {
      $stmt = $conn->prepare("UPDATE videos SET transcript = ? WHERE video_id = ? AND course_id = ?");
      $stmt->bind_param("sii", $transcript, $videoId, $courseId);
      $stmt->execute();
      $stmt->close();
    }
    $successMessage = "Existing video's transcripts updated successfully!";
  }

  // Handle file deletion for videos
  if (isset($_POST['delete_video'])) {
    $videoId = $_POST['delete_video'];
    $stmt = $conn->prepare("SELECT video_title FROM videos WHERE video_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $videoId, $courseId);
    $stmt->execute();
    $stmt->bind_result($videoTitle);
    $stmt->fetch();
    $stmt->close();

    $videoFilePath = 'courses/' . $courseTitle . '/' . $videoTitle;
    if (file_exists($videoFilePath) && unlink($videoFilePath)) {
      $stmt = $conn->prepare("DELETE FROM videos WHERE video_id = ? AND course_id = ?");
      $stmt->bind_param("ii", $videoId, $courseId);
      $stmt->execute();
      $stmt->close();
      $successMessage = "Video and its transcript deleted successfully!";
    } else {
      $errorMessage = "Failed to delete video file.";
    }
  }

  // if (empty($_FILES['new_videos']['name'][0])) {
  //   echo "empty";
  // }

  // Handle new video uploads
  if (!empty($_FILES['course_videos']['name'][0])) {
    foreach ($_FILES['course_videos']['name'] as $key => $videoFileName) {
      $videoFilePath = $courseDir . '/' . basename($videoFileName);
      $transcript = mysqli_real_escape_string($conn, $_POST['transcript'][$key]);

      if (move_uploaded_file($_FILES['course_videos']['tmp_name'][$key], $videoFilePath)) {
        $insertVideoQuery = "INSERT INTO videos (course_id, video_title, transcript) VALUES ($courseId, '$videoFileName', '$transcript')";
        if (mysqli_query($conn, $insertVideoQuery)) {
          $videoUploadSuccess = "Videos and transcripts uploaded successfully.";
        } else {
          $videoUploadError = "Database insert error: " . mysqli_error($conn);
        }
      } else {
        $videoUploadError = "Failed to upload video: $videoFileName.";
      }
    }
  } else {
    $videoUploadError = "No files uploaded.";
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


        <!-- Display existing videos with transcripts -->
        <div>
          <label>Existing Videos and Transcripts:</label>
          <?php
          $videoQuery = $conn->query("SELECT video_id, video_title, transcript FROM videos WHERE course_id = $courseId");
          while ($video = $videoQuery->fetch_assoc()) {
            echo '<p>' . htmlspecialchars($video['video_title']) . ' <button type="submit" name="delete_video" value="' . htmlspecialchars($video['video_id']) . '">Delete</button></p>';
            echo '<textarea class="existing_video_transcripts" name="existing_video_transcripts[' . $video['video_id'] . ']" rows="3">' . htmlspecialchars($video['transcript']) . '</textarea>';
          }
          ?>
        </div>

        <!-- add new video and transcript section -->
        <div id="video-section">
          <div class="video-transcript-group">
            <div class="video-upload">
              <label for="course_videos">Upload Course Video:</label>
              <input type="file" id="course_videos" name="course_videos[]" accept="video/*">
            </div>
            <div class="transcript-upload">
              <label for="transcript">Transcript:</label>
              <textarea id="transcript" name="transcript[]"></textarea>
              <button type="button" class="remove-video">- Video</button>
            </div>
          </div>
        </div>
        <button type="button" id="add-video" class="toggle-button">+ Video</button>


        <button class="save-btn" type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <?php include "../utility/footer.php" ?>
  <script src="../js/script.js"></script>

  <script>
    document.getElementById("add-video").addEventListener("click", function() {
      const videoSection = document.getElementById("video-section");
      const videoTranscriptGroup = document.createElement("div");
      videoTranscriptGroup.classList.add("video-transcript-group");

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

      const transcriptUploadDiv = document.createElement("div");
      transcriptUploadDiv.classList.add("transcript-upload");
      const transcriptLabel = document.createElement("label");
      transcriptLabel.innerHTML = "Transcript:";
      const transcriptInput = document.createElement("textarea");
      transcriptInput.name = "transcript[]";
      transcriptUploadDiv.appendChild(transcriptLabel);
      transcriptUploadDiv.appendChild(transcriptInput);

      const removeButton = document.createElement("button");
      removeButton.type = "button";
      removeButton.classList.add("remove-video");
      removeButton.innerHTML = "- Video";
      removeButton.addEventListener("click", function() {
        videoTranscriptGroup.remove();
      });

      videoTranscriptGroup.appendChild(videoUploadDiv);
      videoTranscriptGroup.appendChild(transcriptUploadDiv);
      videoTranscriptGroup.appendChild(removeButton);

      videoSection.appendChild(videoTranscriptGroup);
    });
  </script>

</body>

</html>



<?php
// Close the database connection
mysqli_close($conn);
?>