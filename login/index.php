<?php 
include '../libs/inc/head.inc.php';
include '../libs/inc/process_login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo_head("Login") ?>
	<script src="../libs/js/validate_login.js"></script>
</head>
<body class="login-page">
<div id="pagewrapper">
	<div id="welcome">Login and <img src="images/keepup-logo-small.png"></div>
<div id="login">
	<form method="post" action="">
		<input type="text" name="login_id" id="loginIdField" placeholder="Student ID or Email" />
		<input type="password" name="password" id="passwordField" placeholder="Password" />
		<p id="status"><?php if(isset($error)){echo $error;}else{echo "&nbsp;";} ?></p>
		<input type="submit" name="login" value="Log in" />
	</form>
</div>
</div>
</body>
</html>

