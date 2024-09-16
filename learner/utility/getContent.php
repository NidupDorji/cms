<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../../utility/db.php";

// Get the content type from the AJAX request
$contentType = $_GET['content_type'];
$video_title = $_GET['video_title'];
$videoID = null;
$courseId = intval($_GET['course_id']);
$userID = intval($_GET['user_id']);
$courseTitle = mysqli_real_escape_string($conn, $_GET['course_title']);

// Prepare the query based on the content type
switch ($contentType) {
  case 'materials':
    $pdfQuery = "SELECT material_title FROM materials WHERE course_id = $courseId AND material_title LIKE '%.pdf'";
    $docxQuery = "SELECT material_title FROM materials WHERE course_id = $courseId AND material_title LIKE '%.docx'";
    $pdfResult = $conn->query($pdfQuery);
    $docxResult = $conn->query($docxQuery);

    $response = '<h4>PDFs</h4><ul>';
    while ($pdf = $pdfResult->fetch_assoc()) {
      $pdfName = htmlspecialchars($pdf['material_title']);
      $pdfPath = "../teacher/courses/$courseTitle/$pdfName";
      $response .= "<li><a href='$pdfPath' target='_blank'>$pdfName</a></li>";
    }
    $response .= '</ul><h4>DOCXs</h4><ul>';
    while ($docx = $docxResult->fetch_assoc()) {
      $docxName = htmlspecialchars($docx['material_title']);
      $docxPath = "../teacher/courses/$courseTitle/$docxName";
      $response .= "<li><a href='$docxPath' target='_blank'>$docxName</a></li>";
    }
    $response .= '</ul>';
    echo $response;
    break;

  case 'notes-display-only':
    $videoIDQuery = "SELECT video_id from videos where video_title='$video_title' and course_id=$courseId";
    $videoIDResult = $conn->query($videoIDQuery);
    if ($videoIDResult->num_rows > 0) {
      // Fetch the video_id from the result set
      $row = $videoIDResult->fetch_assoc();
      $videoID = $row['video_id']; // Access the video_id column

      echo "inside NOTE DISPLAY--> videoID: $videoID\n";

      // Fetch existing notes for the course and user
      $notesQuery = "SELECT note_id, note FROM notes WHERE user_id = $userID AND course_id = $courseId AND video_id = $videoID";
      $notesResult = $conn->query($notesQuery);

      // Create an interface to display notes and add new ones
      $response = '<h4>Your Notes</h4>';
      if ($notesResult->num_rows > 0) {
        while ($note = $notesResult->fetch_assoc()) {
          $noteId = $note['note_id'];
          $noteText = htmlspecialchars($note['note']);
          $response .= "<div class='note-item' data-note-id='$noteId'>
                            <textarea class='note-text'>$noteText</textarea>
                            <button class='save-note'>Save</button>
                            <button class='delete-note'>Delete</button>
                          </div>
                          ";
        }
      } else {
        $response .= "<p>No notes added.</p>";
      }

      // Add a section to add new notes
      $response .= "<h4>Add a New Note</h4>
                    <textarea id='new-note-text' class='new-note-text'></textarea>
                    <button class='add-new-note'>Add Note</button>";

      echo $response;
      break;
    } else {
      echo "No video found with the given title and course.";
    }
    break;

    break;



  case 'notes':
    if (empty($video_title)) {
      echo "Please select a video to view its note.";
    } else {
      // echo "inside NOTES--> video_title:$video_title \n";
      // echo "inside NOTES--> course_id:$courseId \n";
    }
    $videoIDQuery = "SELECT video_id from videos where video_title='$video_title' and course_id=$courseId";
    $videoIDResult = $conn->query($videoIDQuery);
    if ($videoIDResult->num_rows > 0) {
      // Fetch the video_id from the result set
      $row = $videoIDResult->fetch_assoc();
      $videoID = $row['video_id']; // Access the video_id column

      // echo "inside NOTES-->  videoID: $videoID\n";

      // Fetch existing notes for the course and user
      $notesQuery = "SELECT note_id, note FROM notes WHERE user_id = $userID AND course_id = $courseId AND video_id = $videoID";
      $notesResult = $conn->query($notesQuery);

      // Create an interface to display notes and add new ones
      $response = '<h4>Your Notes</h4>';
      if ($notesResult->num_rows > 0) {
        while ($note = $notesResult->fetch_assoc()) {
          $noteId = $note['note_id'];
          $noteText = htmlspecialchars($note['note']);
          $response .= "<div class='note-item' data-note-id='$noteId'>
                            <textarea class='note-text'>$noteText</textarea>
                            <button class='save-note'>Save</button>
                            <button class='delete-note'>Delete</button>
                          </div>
                          ";
        }
      } else {
        $response .= "<p>No notes added.</p>";
      }

      // Add a section to add new notes
      $response .= "<h4>Add a New Note</h4>
                    <textarea id='new-note-text' class='new-note-text'></textarea>
                    <button class='add-new-note'>Add Note</button>";

      echo $response;
      break;
    } else {
      echo "No video found with the given title and course.";
    }
    break;

  case 'discuss':
    // Since you don't have data yet, return a message
    echo "<p>No discussion data available for this course.</p>";
    break;
  default:
    echo "Invalid content type";
    break;
}


// Close the connection
$conn->close();
