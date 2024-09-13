<div class="my-courses">
  <h2>My Courses</h2>
  <?php

  // Retrieve courses created by the current user
  $userId = $_SESSION['user_id'];

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  try {
    // Modify the query to select course_id, course_title, course_description, and likes
    $query = "SELECT course_id, course_title, course_description, likes FROM courses WHERE created_by = ?";

    // Prepare the statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
      throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    // Bind parameters (assuming $userId is an integer)
    $stmt->bind_param("i", $userId);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the data
    if (mysqli_num_rows($result) > 0) {
      echo '<table>';
      echo '<thead>';
      echo '<tr>';
      echo '<th>Course Title</th>';
      echo '<th>Course Description</th>';
      echo '<th>Likes</th>';  // New column header for likes
      echo '<th>Action</th>';  // Edit button column
      echo '</tr>';
      echo '</thead>';
      echo '<tbody>';
      while ($course = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($course['course_title']) . '</td>';
        echo '<td>' . htmlspecialchars($course['course_description']) . '</td>';
        echo '<td>' . htmlspecialchars($course['likes']) . '</td>';  // Display number of likes
        // Edit button with link to edit page
        echo '<td><a href="edit_course.php?id=' . htmlspecialchars($course['course_id']) . '" class="edit-button">Edit</a></td>';
        echo '</tr>';
      }
      echo '</tbody>';
      echo '</table>';
    } else {
      echo '<p>No courses found.</p>';
    }

    // Close the statement
    $stmt->close();
  } catch (mysqli_sql_exception $e) {
    // Catch any SQL-related exceptions
    echo "Error: " . $e->getMessage();
  } catch (Exception $e) {
    // Catch any other exceptions
    echo "An error occurred: " . $e->getMessage();
  }

  // Close database connection
  mysqli_close($conn);
  ?>
</div>