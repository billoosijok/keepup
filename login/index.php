<?php 
// session_start();
require '../libs/inc/classes.php';

require '../libs/inc/process_login.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php 
	// page::head("Login");
	$jqueryLink = "../libs/js/jquery-2.2.3.min.js";
	$cssLink = "../libs/css/style.css";
	$functionsLink = "../libs/js/functions.js";
	$transitionsLink = "../libs/js/transitions.js";
	
?>
	<meta charset="UTF-8">
	<title>Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=1, user-scalable=no">
	
	<link rel="stylesheet" type="text/css" href="<?php echo $cssLink; ?>">
	<script src="<?php echo $jqueryLink ?>"></script>
	<script src="<?php echo $functionsLink ?>"></script>
	<script src="<?php echo $transitionsLink ?>"></script>
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

