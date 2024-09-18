<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection
include "../../utility/db.php";

// Define the number of courses per page
$coursesPerPage = 4;

// Get search query and page number from AJAX request
$searchQuery = isset($_GET['query']) ? trim(mysqli_real_escape_string($conn, $_GET['query'])) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $coursesPerPage;

// Fetch courses based on search query and page number
$query = "SELECT course_id, course_title, course_description, thumbnail_path FROM courses 
          WHERE course_title LIKE '%$searchQuery%' OR course_description LIKE '%$searchQuery%' 
          LIMIT $start, $coursesPerPage";
$result = mysqli_query($conn, $query);

// Prepare data to return
$courses = [];
if (mysqli_num_rows($result) > 0) {
  while ($course = mysqli_fetch_assoc($result)) {
    $course['thumbnail_path'] = '../teacher/courses/' . $course['course_title'] . '/thumbnail/' . $course['thumbnail_path'];
    $courses[] = $course;
  }
}

// Get the total number of courses
$totalCoursesQuery = "SELECT COUNT(*) as total FROM courses WHERE course_title LIKE '%$searchQuery%' OR course_description LIKE '%$searchQuery%'";
$totalResult = mysqli_query($conn, $totalCoursesQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalCourses = $totalRow['total'];

// Return the data as JSON
echo json_encode([
  'courses' => $courses,
  'totalPages' => ceil($totalCourses / $coursesPerPage),
  'currentPage' => $page
]);
