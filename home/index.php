<?php session_start();

if(isset($_GET['logout'])) {
	session_unset();
}
require '../libs/inc/db_connect.php';
include '../libs/inc/head.inc.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
		echo_head("Your Home Page");
	?>
<script>Transition.adopt();</script>
</head>
<body class="home">
	<?php

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
					header('Location: admin/admin.php');
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
		header("Location: ../index.php");
	}


?>
</body>
</html>

