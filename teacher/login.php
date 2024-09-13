<?php include('server.php'); ?>

<!DOCTYPE html>
<html>

<head>
	<title>Teacher Registration system</title>
	<link rel="stylesheet" type="text/css" href="../css/login.css">
</head>

<body>


	<form method="post" action="login.php">
		<!-- <div class="header"> -->
		<h2 style="text-align:center;">🎓Teacher Login</h2>
		<?php include('../utility/errors.php'); ?>
		<div class="input-group">
			<label>Username</label>
			<input type="text" name="username">
		</div>
		<div class="input-group">
			<label>Password</label>
			<input type="password" name="password">
		</div>
		<div class="input-group">
			<button type="submit" class="btn" name="login_user">Login</button>
		</div>
		<p>
			Not yet a member? <a href="register.php">Sign up</a>
		</p>
	</form>
</body>

</html>