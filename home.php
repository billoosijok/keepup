<?php 

session_start();
if(isset($_GET['logout'])) {
	session_unset();
}
require 'inc/db_connect.php';
include 'inc/head.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
		echo_head("Your Home Page");
	?>
</head>
<body class="home">
	<?php
	
	// MARK:
	if(isset($_SESSION['user_id'])) {

		$user_id = $_SESSION['user_id'];

		$sql = "SELECT role FROM `users` WHERE user_id='$user_id'";

		$result = $dbc->query($sql);

		if($row = $result->fetch_row()) {
			$role = $row[0];

			switch ($role) {
				case 'S':
					$role = "Student";
					break;

				case 'T':
					$role = "Teacher";
					break;

				case 'A':
					$role = "Admin";
					break;

				default:
					echo "error";
					break;
			}
		}
		?>
		<a href="?logout" id="logout">Logout</a>
		<p id="intro"><?php echo $role ?> website</p>
		<?php

	} else {
		header("Location: index.php");
	}


?>
</body>
</html>

