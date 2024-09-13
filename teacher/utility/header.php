<header>
  <div class="logo">
    <h1>🎓CONTENT MANAGEMENT SYSTEM</h1>
  </div>
  <nav>
    <a href="home.php">Home </a>
    <a href="#">Dashboard</a>
    <a href="#" id="theme-toggle"><i class="fas fa-adjust"></i></a> <!-- Light/Dark Mode Toggle -->

    <div class="search-container">
      <input type="text" placeholder="Search..." aria-label="Search">
      <button type="submit">Search</button>
    </div>
    <a href="#" id="language-icon"><i class="fas fa-globe"></i></a>
    <div id="language-dropdown" class="dropdown-content">
      <a href="#">English</a>
      <a href="#">Dzongkha</a>
    </div>
    <!-- Add more languages as needed -->
    </div>
    <a href="#" id="notification-icon"><i class="fas fa-bell"></i></a>
    <div id="notification-container" class="notification-hidden">
      <p style="color: black;">You have new notifications!</p>
      <!-- Add more notification messages as needed -->
    </div>
    <a href="#" id="profile-icon"><i class="fas fa-user"></i></a>
    <div id="profile-dropdown" class="dropdown-content profile-dropdown">
      <a href="profile.php">Profile</a>
      <a href="#">Calendar</a>
      <a href="#">Update</a>
      <a href="#" id="profile-link">Help Center</a>
      <a href="home.php?logout='1' id=">Logout</a>
    </div>
  </nav>
</header>