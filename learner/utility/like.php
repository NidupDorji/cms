<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// update_like.php
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "cms"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "db connected";

$user_id = intval($_POST['user_id']);
$course_id = intval($_POST['course_id']);
$liked = $_POST['liked'] === 'true'; // Convert to boolean

if ($liked) {
  // Check if the user already liked the course
  $check_like = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND course_id = ?");
  $check_like->bind_param("ii", $user_id, $course_id);
  $check_like->execute();
  $check_like->store_result();

  if ($check_like->num_rows === 0) {
    // Insert like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $stmt->close();

    // Increment likes count in courses table
    $update_likes = $conn->prepare("UPDATE courses SET likes = likes + 1 WHERE course_id = ?");
    $update_likes->bind_param("i", $course_id);
    $update_likes->execute();
    $update_likes->close();
  }
  $check_like->close();
} else {
  // Check if the user already liked the course
  $check_like = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND course_id = ?");
  $check_like->bind_param("ii", $user_id, $course_id);
  $check_like->execute();
  $check_like->store_result();

  if ($check_like->num_rows > 0) {
    // Remove like
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $stmt->close();

    // Decrement likes count in courses table
    $update_likes = $conn->prepare("UPDATE courses SET likes = likes - 1 WHERE course_id = ?");
    $update_likes->bind_param("i", $course_id);
    $update_likes->execute();
    $update_likes->close();
  }
  $check_like->close();
}

$conn->close();

echo json_encode(["success" => true]);
