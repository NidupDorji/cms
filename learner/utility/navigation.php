<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<div class="container">
  <div class="header-container">
    <h2 class="header-title">Available Courses</h2>
  </div>
  <div class="courses" id="course-container">
    <!-- Courses will be loaded here via AJAX -->
  </div>
</div>

<!-- Pagination Navigation -->
<div class="pagination" id="pagination-container">
  <!-- Pagination links will be loaded here via AJAX -->
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    let currentQuery = ''; // Initialize search query

    // Function to load courses based on page number and search query
    function loadCourses(page = 1, query = '') {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', `utility/fetch_available_courses.php?page=${page}&query=${encodeURIComponent(query)}`, true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          displayCourses(response.courses);
          displayPagination(response.totalPages, response.currentPage, query);
        }
      };
      xhr.send();
    }

    // Function to display courses
    function displayCourses(courses) {
      const courseContainer = document.getElementById('course-container');
      courseContainer.innerHTML = ''; // Clear previous courses
      if (courses.length === 0) {
        courseContainer.innerHTML = '<p>No courses found matching your search criteria.</p>';
        return;
      }

      courses.forEach(course => {
        const courseHtml = `
        <div class="course">
          <a href="product.php?course_id=${course.course_id}">
            <img src="${course.thumbnail_path}" alt="${course.course_title}">
            <div class="course-info">
              <h3>${course.course_title}</h3>
              <p>${course.course_description}</p>
            </div>
          </a>
        </div>`;
        courseContainer.insertAdjacentHTML('beforeend', courseHtml);
      });
    }

    // Function to display pagination
    function displayPagination(totalPages, currentPage, query) {
      const paginationContainer = document.getElementById('pagination-container');
      paginationContainer.innerHTML = ''; // Clear previous pagination

      if (currentPage > 1) {
        paginationContainer.innerHTML += `<a href="#" class="prev" data-page="${currentPage - 1}" data-query="${query}">Previous</a>`;
      }

      for (let i = 1; i <= totalPages; i++) {
        const activeClass = (i === currentPage) ? 'active' : '';
        paginationContainer.innerHTML += `<a href="#" class="${activeClass}" data-page="${i}" data-query="${query}">${i}</a>`;
      }

      if (currentPage < totalPages) {
        paginationContainer.innerHTML += `<a href="#" class="next" data-page="${currentPage + 1}" data-query="${query}">Next</a>`;
      }
    }

    // Handle pagination click
    document.getElementById('pagination-container').addEventListener('click', function(e) {
      if (e.target.tagName === 'A') {
        e.preventDefault();
        const page = e.target.getAttribute('data-page');
        const query = document.querySelector('input[name="query"]').value; // Get the search query from the form input
        loadCourses(page, query); // Pass the current query when pagination is clicked
      }
    });

    // Handle search form submission
    const searchForm = document.querySelector('.search-form');
    searchForm.addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent the form from submitting the traditional way
      const query = document.querySelector('input[name="query"]').value; // Get the search query from the input field
      currentQuery = query; // Store the current query
      loadCourses(1, query); // Load courses based on search query
    });

    // Load initial courses (either from search or default)
    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('query') || ''; // Get the search query from URL if present
    currentQuery = initialQuery;
    loadCourses(1, initialQuery);
  });
</script>


<style>
  /* css for top demanding course nameplate */
  /* Centering the container and adding margin */


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