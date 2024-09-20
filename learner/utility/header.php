<header>
  <div class="hamburger-menu" id="hamburger-menu">
    <i class="fas fa-bars"></i>
  </div>
  <div class="logo">
    <h1>📘TUKULEE</h1>
  </div>
  <nav id="nav-menu">
    <div class="search-container">
      <form action="home.php" method="GET" class="search-form">
        <input type="text" name="query" placeholder="Search..." aria-label="Search">
        <button type="submit" style="display:none;"></button>
      </form>
    </div>
    <a href="#" id="profile-icon"><i class="fas fa-user" title="Home"></i></a>
    <div id="profile-dropdown" class="dropdown-content profile-dropdown">
      <a href="profile.php">Profile</a>
      <a href="#">Calendar</a>
      <a href="#">Update</a>
      <a href="#" id="profile-link">Help Center</a>
      <a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout='1'">Logout</a>
    </div>
    <a href="home.php" title="Home"><i class="fas fa-home"></i></a>
    <a href="#" id="notification-icon" title="Notifications"><i class="fas fa-bell"></i></a>
    <div id="notification-dropdown" class="dropdown-content notification-dropdown">
      <p>New notifications!</p>
    </div>
    <a href="#" class="nav-icon" id="categories-icon" title="Categories"><i class="fas fa-th-list"></i></a>
    <div id="language-dropdown" class="dropdown-content language-dropdown">
      <a href="#">English</a>
      <a href="#">Dzongkha</a>
    </div>
    <a href="#" class="nav-icon" id="dashboard-icon" title="Dashboard"><i class="fas fa-tachometer-alt"></i></a>
    <a href="#" id="language-icon" title="Language"><i class="fas fa-globe"></i></a>
    <a href="#" id="theme-toggle" title="Theme toggle"><i class="fas fa-adjust"></i></a>
  </nav>

</header>