<header>
  <div class="logo">
    <h1>📘CONTENT MANAGEMENT SYSTEM</h1>
  </div>
  <nav>
    <a href="home.php">Home</a>
    <a href="#">Dashboard</a>
    <!-- <a href="#">Recently Accessed Courses</a> -->
    <!-- <a href="#">Calendar</a> -->
    <a href="#">Categories</a>
    <div class="search-container">
      <form action="home.php" method="GET" class="search-form">
        <input type="text" name="query" placeholder="Search..." aria-label="Search">
        <button type="submit">Search</button>
      </form>
    </div>
    <a href="#" id="theme-toggle"><i class="fas fa-adjust"></i></a> <!-- Light/Dark Mode Toggle -->
    <a href="#" id="language-icon"><i class="fas fa-globe"></i></a>
    <div id="language-dropdown" class="dropdown-content">
      <a href="#">English</a>
      <a href="#">Dzongkha</a>
    </div>
    </div>
    <a href="#" id="notification-icon"><i class="fas fa-bell"></i></a>
    <div id="notification-container" class="notification-hidden">
      <p style="color: black;">You have new notifications!</p>
    </div>
    <a href="#" id="profile-icon"><i class="fas fa-user"></i></a>
    <div id="profile-dropdown" class="dropdown-content profile-dropdown">
      <a href="profile.php">Profile</a>
      <a href="#">Calendar</a>
      <a href="#">Update</a>
      <a href="#" id="profile-link">Help Center</a>
      <a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout='1'">Logout</a>
    </div>
  </nav>
</header>