//nav menu toggle using hamburger menu icon
document.getElementById('hamburger-menu').onclick = function() {
    const navMenu = document.getElementById('nav-menu');
    navMenu.classList.toggle('active');
}
  // Close the nav menu when clicking outside of it
    document.addEventListener('click', function(event) {
    const navMenu = document.getElementById('nav-menu');
    const hamburgerMenu = document.getElementById('hamburger-menu');

    // Check if the click was outside the nav menu and hamburger menu
    if (!navMenu.contains(event.target) && !hamburgerMenu.contains(event.target) && navMenu.classList.contains('active')) {
        navMenu.classList.remove('active');
    }
});
// Helper function to position dropdowns
function positionDropdown(dropdown, icon) {
    const navMenu = document.getElementById('nav-menu');
    
    // Check if nav-menu is active (for mobile) and adjust the dropdown position
    if (navMenu.classList.contains('active')) {
        dropdown.style.left = (icon.offsetLeft + icon.offsetWidth) + 'px';
        dropdown.style.top = icon.offsetTop + 'px';
    } else {
        // For larger screens, reset the left position
        dropdown.style.left = '';
        dropdown.style.right = '0';
        dropdown.style.top = '';
    }
}


// Toggle the language dropdown menu when the language icon is clicked
document.getElementById('language-icon').addEventListener('click', function(event) {
    event.preventDefault();
    var dropdown = document.getElementById('language-dropdown');
    positionDropdown(dropdown, event.target); // Position the dropdown
    dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
});

// Toggle the profile dropdown menu when the profile icon is clicked
document.getElementById('profile-icon').addEventListener('click', function(event) {
    event.preventDefault();
    var profileDropdown = document.getElementById('profile-dropdown');
    positionDropdown(profileDropdown, event.target); // Position the dropdown
    profileDropdown.style.display = profileDropdown.style.display === 'none' || profileDropdown.style.display === '' ? 'block' : 'none';
});

// Toggle the notification dropdown when the notification icon is clicked
document.getElementById('notification-icon').addEventListener('click', function(event) {
    event.preventDefault();
    var notificationDropdown = document.getElementById('notification-dropdown');
    positionDropdown(notificationDropdown, event.target); // Position the dropdown
    notificationDropdown.style.display = notificationDropdown.style.display === 'none' || notificationDropdown.style.display === '' ? 'block' : 'none';
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    var languageDropdown = document.getElementById('language-dropdown');
    var profileDropdown = document.getElementById('profile-dropdown');
    var notificationDropdown = document.getElementById('notification-dropdown');
    
    if (!event.target.closest('#language-icon') && !event.target.closest('#language-dropdown')) {
        languageDropdown.style.display = 'none';
    }
    if (!event.target.closest('#profile-icon') && !event.target.closest('#profile-dropdown')) {
        profileDropdown.style.display = 'none';
    }
    if (!event.target.closest('#notification-icon') && !event.target.closest('#notification-dropdown')) {
        notificationDropdown.style.display = 'none';
    }
});

//theme switch b/w light & dark mode
document.getElementById('theme-toggle').addEventListener('click', function() {
document.body.classList.toggle('dark-mode');
// Store the theme in localStorage
const isDarkMode = document.body.classList.contains('dark-mode');
localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
});

// Load the saved theme on page load
window.addEventListener('load', function() {
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
}
});













