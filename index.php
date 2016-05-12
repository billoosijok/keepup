<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php require 'inc/head.inc.php'; 
	echo_head('Login to KeepUp')?>
	<script src="js/validate_login.js"></script>
</head>
<body>
	<main id="pagewrapper">
		<?php 

		if(isset($_SESSION['user_id'])) { 
			header("Location: home.php");
			?>
			<?php } else { ?>
			<div id="welcome">Login and <img src="resources/keepup-logo-small.png"></div>
			<div id="login">
				<form method="post" action="inc/process_login.php">
					<input type="text" name="login_id" id="loginIdField" placeholder="Student ID or Email" />
					<input type="password" name="password" id="passwordField" placeholder="Password" />
					<p id="status">&nbsp;</p>
					<input type="submit" name="login" value="Log in" />
				</form>
			</div>
			<?php 
		} 
		?>
	</main>
</body>
</html>