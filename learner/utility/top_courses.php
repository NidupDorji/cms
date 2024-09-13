<!-- display top 3 courses. -->
<div class="header-container">
  <?php if (!isset($_GET['query']) || empty(trim($_GET['query']))): ?>
    <h2 class="header-title">Top MOST DEMANDING Courses</h2>
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

<style>
  /* Header styling */
  .header-container {
    text-align: center;
    margin: 20px 0;
    position: relative;
    /* Needed for pseudo-element positioning */
  }

  .header-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 25%, rgba(255, 255, 255, 0) 75%);
    opacity: 0.5;
    pointer-events: none;
    /* Allows interaction with underlying elements */
    z-index: 1;
  }

  .header-title {
    position: relative;
    /* Needed to ensure it stays above the shiny effect */
    display: inline-block;
    font-size: 2em;
    font-weight: bold;
    color: gold;
    margin: 0;
    padding: 10px 20px;
    border: 2px solid gold;
    border-radius: 8px;
    background: gold;
    animation: fadeIn 1s ease-in-out;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    /* Add shadow for depth */
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }


  .top-courses-container {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px auto;
    padding: 16px;
    max-width: 1200px;
  }

  .top-courses {
    position: relative;
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    width: 300px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .top-courses:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
  }

  .top-courses img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .top-courses .course-info {
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  .top-courses h3 {
    font-size: 1.5em;
    margin: 16px 0;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .top-courses p {
    font-size: 1em;
    margin: 8px 0;
    color: #666;
  }

  .top-courses .likes {
    font-size: 1.2em;
    font-weight: bold;
    color: #ffd700;
    /* Gold color */
    margin-top: 10px;
  }

  .top-courses::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(to right, #ffd700, #ffcc33, #daa520);
  }

  @media (max-width: 768px) {
    .top-courses-container {
      flex-direction: column;
      /* Stack vertically on small screens */
      align-items: center;
    }

    .top-courses {
      max-width: 100%;
      /* Full width for smaller screens */
      margin-bottom: 20px;
      /* Space between stacked items */
    }

    .top-courses:last-child {
      margin-bottom: 0;
      /* No margin for the last item */
    }
  }
</style>