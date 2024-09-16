<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../../utility/db.php";

session_start();
$userID = intval($_POST['user_id']);
$courseId = intval($_POST['course_id']);
$video_title = mysqli_real_escape_string($conn, $_POST['video_title']);

// Get video_id if it's needed
$videoID = null;
$videoIDQuery = "SELECT video_id FROM videos WHERE video_title = '$video_title' AND course_id = $courseId";
$videoIDResult = $conn->query($videoIDQuery);
if ($videoIDResult->num_rows > 0) {
  $row = $videoIDResult->fetch_assoc();
  $videoID = $row['video_id'];
} else {
  echo "No video found with the given title and course.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $action = $_POST['action'];

  echo "inside if server=requuest===post  case\n";  // Debugging
  $noteId = intval($_POST['note_id']);
  // echo "Received note_id: $noteId\n";
  // echo "Received user_id: $userID\n";

  switch ($action) {
    case 'add':
      $noteText = mysqli_real_escape_string($conn, $_POST['note_text']);
      $query = "INSERT INTO notes (user_id, course_id, video_id, note) VALUES ($userID, $courseId, $videoID, '$noteText')";
      if ($conn->query($query)) {
        echo "Note added successfully.";
      } else {
        echo "Error adding note: " . $conn->error;
      }
      break;

    case 'edit':
      $noteId = intval($_POST['note_id']);
      $noteText = mysqli_real_escape_string($conn, $_POST['note_text']);
      $query = "UPDATE notes SET note = '$noteText' WHERE note_id = $noteId AND user_id = $userID";
      if ($conn->query($query)) {
        echo "Note updated successfully.";
      } else {
        echo "Error updating note: " . $conn->error;
      }
      break;

    case 'delete':
      echo "inside delete case\n";  // Debugging
      $noteId = intval($_POST['note_id']);
      $query = "DELETE FROM notes WHERE note_id = $noteId AND user_id = $userID";
      if ($conn->query($query)) {
        echo "Note deleted successfully.\n";
      } else {
        echo "Error deleting note: " . $conn->error;  // Output SQL error for debugging
      }
      break;
  }
}

$conn->close();
