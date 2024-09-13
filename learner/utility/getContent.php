<?php
include "../../utility/db.php";

// Get the content type from the AJAX request
$contentType = $_GET['content_type'];
$courseId = intval($_GET['course_id']);
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
  case 'notes':
    // Since you don't have data yet, return a message
    echo "<p>No notes  available for this course.</p>";
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
