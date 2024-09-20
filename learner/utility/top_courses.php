<!-- display top 3 courses. -->
<div class="header-container">
  <?php if (!isset($_GET['query']) || empty(trim($_GET['query']))): ?>
    <h2 class="header-title">Top POPULAR Courses</h2>
  <?php endif; ?>
</div>
<div class="top-courses-container">
  <?php
  $conn = mysqli_connect('localhost', 'root', '', 'cms');
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
  $query = "SELECT course_title, course_id, course_description, thumbnail_path, likes FROM courses ORDER BY likes DESC LIMIT 3";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "<div class='top-courses'>
        <a href='product.php?course_id=" . $row['course_id'] . "' class='course'>
          <img src='../teacher/courses/" . $row['course_title'] . "/thumbnail/" . $row['thumbnail_path'] . "' alt='" . $row['course_title'] . "'>
          <div class='course-info'>
            <h3>" . $row['course_title'] . "</h3>
            <p>" . $row['course_description'] . "</p>
            <strong class='likes'>Likes: " . $row['likes'] . "</strong>
          </div>
        </a>
      </div>";
    }
  } else {
    echo "<p>No courses available</p>";
  }
  ?>
</div>