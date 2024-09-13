<?php
// Database connection
include "../../utility/db.php";

if (isset($_GET['video_title']) && isset($_GET['course_id'])) {
  $videoTitle = $_GET['video_title'];
  $courseId = intval($_GET['course_id']);

  // Query to get the transcript for the selected video
  $query = "SELECT transcript FROM videos WHERE video_title = ? AND course_id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('si', $videoTitle, $courseId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    echo htmlspecialchars($row['transcript']); // Output the transcript safely
  } else {
    echo "Transcript not found for this video.";
  }

  $stmt->close();
} else {
  echo "Invalid video selection.";
}

$conn->close();
