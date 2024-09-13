<?php
session_start();

// Enable error reporting in your PHP code to get more detailed error messages
error_reporting(E_ALL);
ini_set('display_errors', 1);

// initializing variables
$username = "";
$email    = "";
$errors = array();
$role_id = 0;

// Database connection
include "../utility/db.php";


// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = $conn->real_escape_string($_POST['username']);
  $email = $conn->real_escape_string($_POST['email']);
  $password_1 = $conn->real_escape_string($_POST['password_1']);
  $password_2 = $conn->real_escape_string($_POST['password_2']);

  // form validation: ensure that the form is correctly filled
  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password_1)) {
    array_push($errors, "Password is required");
  }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = $conn->query($user_check_query);
  $user = $result->fetch_assoc();

  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }
    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Finding role_id
  switch ($_SESSION['role']) {
    case 'admin':
      $role_id = 1;
      break;
    case 'teacher':
      $role_id = 2;
      break;
    case 'learner':
      $role_id = 3;
      break;
    default:
      $role_id = 0; // Default value for invalid roles
      break;
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    $password = md5($password_1); // Encrypt the password before saving in the database
    $query = "INSERT INTO users (username, password_hash, email, role_id) VALUES('$username', '$password', '$email', $role_id)";
    $conn->query($query);

    // Fetch user details to create session
    $query = "SELECT * FROM users WHERE username='$username' AND password_hash='$password'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    $_SESSION['email'] = $row['email'];
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['role_id'] = $role_id;
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You have registered successfully and logged in.";
    header('location: pre_home.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = $conn->real_escape_string($_POST['username']);
  $password = $conn->real_escape_string($_POST['password']);

  $query = "SELECT role_id FROM users WHERE username='$username' LIMIT 1";
  $result = $conn->query($query);

  if ($result->num_rows) {
    $row = $result->fetch_assoc(); // Fetch the result as an associative array
    $role_id = (int) $row['role_id']; // Convert the role_id to an integer
  } else {
    $role_id = 0; // Default value for invalid username
  }

  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }
  if ($role_id == 0) {
    array_push($errors, "Wrong username/password combination");
  }
  if ($role_id != 2 && $role_id != 0) {
    array_push($errors, "Log in failed! You must log in from Student's Interface.");
  }

  if (count($errors) == 0) {
    $password = md5($password);
    $query = "SELECT * FROM users WHERE username='$username' AND password_hash='$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $_SESSION['username'] = $row['username'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['user_id'] = $row['user_id'];
      $_SESSION['success'] = "You are now logged in";
      $_SESSION['role_id'] = $role_id;
      header('location: home.php');
    } else {
      array_push($errors, "Wrong username/password combination");
    }
  }
}

$conn->close();
