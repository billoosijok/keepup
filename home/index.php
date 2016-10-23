<?php

// To avoid error of starting session twice.
if(!isset($_SESSION)) { session_start(); }

// It's true if a user has logged in.
if(isset($_SESSION['user_info'])) {
	
	## Redirecting the user to their specific portal.
	switch ($_SESSION['user_info']->role) {
		
		case 'S': // S for 'student'
			header('Location: student.php');
			break;
		
		case 'T': // T for 'teacher'
			header('Location: teacher.php');
			break;

		case 'A': // A for 'admin'
			header('Location: admin.php');
			break;
		
		default:
			header("Location: ../index.php");
			break;
	}
		
} else {
	header("Location: ../index.php");
}
