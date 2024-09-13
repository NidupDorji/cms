<div class="container">
  <div class="header-container">
    <?php if (!isset($_GET['query']) || empty(trim($_GET['query']))): ?>
      <h2 class="header-title">Available Courses</h2>
    <?php endif; ?>
  </div>
  <div class="courses">
    <?php
    // Database connection
    $conn = mysqli_connect('localhost', 'root', '', 'cms');
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }

    // Define the number of courses per page
    $coursesPerPage = 6;

    // Get search query from the URL
    $searchQuery = isset($_GET['query']) ? trim(mysqli_real_escape_string($conn, $_GET['query'])) : '';

    // Count the total number of courses with or without search query
    $totalCoursesQuery = "SELECT COUNT(*) as total FROM courses WHERE course_title LIKE '%$searchQuery%' OR course_description LIKE '%$searchQuery%'";
    $totalResult = mysqli_query($conn, $totalCoursesQuery);
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalCourses = $totalRow['total'];

    // Calculate the number of pages required
    $totalPages = ceil($totalCourses / $coursesPerPage);

    // Get the current page number from the URL, default to page 1 if not set
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $coursesPerPage;

    // Fetch courses for the current page with search query
    $query = "SELECT course_id, course_title, course_description,thumbnail_path FROM courses 
              WHERE course_title LIKE '%$searchQuery%' OR course_description LIKE '%$searchQuery%' 
              LIMIT $start, $coursesPerPage";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      while ($course = mysqli_fetch_assoc($result)) {
        $courseId = $course['course_id'];
        $courseTitle = htmlspecialchars($course['course_title']);
        $courseDescription = htmlspecialchars($course['course_description']);
        $thumbnailPath = '../teacher/courses/' . $courseTitle . '/thumbnail/' . $course['thumbnail_path'];
        echo "
        <div class='course'>
          <a href='product.php?course_id=$courseId'>
            <img src='$thumbnailPath' alt='$courseTitle'>
            <div class='course-info'>
              <h3>$courseTitle</h3>
              <p>$courseDescription</p>
            </div>
          </a>
        </div> ";
      }
    } else {
      echo "<p>No courses found matching your search criteria.</p>";
    }
    ?>
  </div>
</div>

<!-- Pagination Navigation -->
<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="?query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $page - 1; ?>" class="prev">Previous</a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="?query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $page + 1; ?>" class="next">Next</a>
  <?php endif; ?>
</div>

<style>
  /* css for top demanding course nameplate */
  /* Centering the container and adding margin */
  .header-container {
    text-align: center;
    margin: 20px 0;
  }

  .header-title {
    display: inline-block;
    font-size: 2em;
    font-weight: bold;
    color: white;
    margin: 0;
    padding: 10px 20px;
    border: 2px solid #5F9EA0;
    border-radius: 8px;
    background: #5F9EA0;
    animation: fadeIn 1s ease-in-out;
  }

  /* Keyframes for fade-in animation */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }


  /* home page navigation  starts*/
  .pagination {
    display: flex;
    justify-content: center;
    padding: 20px 0;
  }

  .pagination a {
    text-decoration: none;
    color: #007bff;
    padding: 10px 15px;
    border: 1px solid #ddd;
    margin: 0 5px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  .pagination a:hover {
    background-color: #007bff;
    color: #fff;
  }

  .pagination a.active {
    background-color: #007bff;
    color: #fff;
    pointer-events: none;
  }

  .pagination a.prev,
  .pagination a.next {
    font-weight: bold;
  }
</style>