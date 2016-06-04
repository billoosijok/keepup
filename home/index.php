<?php
session_start();

if(isset($_SESSION['user_info'])) {
		
		switch ($_SESSION['user_info']->role) {
			case 'S':
				header('Location: student.php');
				break;
			
			case 'T':
				header('Location: teacher.php');
				break;
			
			case 'A':
				header('Location: admin.php');
				break;
			
			default:
				die("error");
				break;
		}
	} else {
		header("Location: ../index.php");
	}
