//// language icon starts
// Toggle the language dropdown menu when the language icon is clicked
document.getElementById('language-icon').addEventListener('click', function(event) {
    event.preventDefault();
    var dropdown = document.getElementById('language-dropdown');
    dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
});

// Close the dropdown when clicking outside of it
document.addEventListener('click', function(event) {
    var languageIcon = document.getElementById('language-icon');
    var dropdown = document.getElementById('language-dropdown');

    // Check if the click was outside the language icon and dropdown
    if (!languageIcon.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
// Hide the profile dropdown menu when clicking outside of it
document.addEventListener('click', function(event) {
    var profileIcon = document.getElementById('profile-icon');
    var profileDropdown = document.getElementById('profile-dropdown');
    if (!profileIcon.contains(event.target) && !profileDropdown.contains(event.target)) {
        profileDropdown.style.display = 'none';
    }
});

//// notification starts
// Toggle the notification container when the notification icon is clicked
document.getElementById('notification-icon').addEventListener('click', function(event) {
    event.preventDefault();
    var notificationContainer = document.getElementById('notification-container');
    notificationContainer.style.display = notificationContainer.style.display === 'none' || notificationContainer.style.display === '' ? 'block' : 'none';
});

// Hide the notification container when clicking outside of it
document.addEventListener('click', function(event) {
    var notificationIcon = document.getElementById('notification-icon');
    var notificationContainer = document.getElementById('notification-container');
    if (!notificationIcon.contains(event.target) && !notificationContainer.contains(event.target)) {
        notificationContainer.style.display = 'none';
    }
});


//// profile starts
// Toggle the profile dropdown menu when the profile icon is clicked
document.getElementById('profile-icon').addEventListener('click', function(event) {
    event.preventDefault();
    var profileDropdown = document.getElementById('profile-dropdown');
    profileDropdown.style.display = profileDropdown.style.display === 'none' || profileDropdown.style.display === '' ? 'block' : 'none';
});

// Hide the profile dropdown menu when clicking outside of it
document.addEventListener('click', function(event) {
    var profileIcon = document.getElementById('profile-icon');
    var profileDropdown = document.getElementById('profile-dropdown');
    if (!profileIcon.contains(event.target) && !profileDropdown.contains(event.target)) {
        profileDropdown.style.display = 'none';
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













