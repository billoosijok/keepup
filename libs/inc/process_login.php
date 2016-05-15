<?php
	session_start();

	require 'db_connect.php';	

	if(!isset($_POST['login_id']) AND !isset($_POST['password'])) {
		header($_SERVER['SERVER_PROTOCOL']. " 404 Not Found", true, 403);
	
	} else {

		$login_id = $_POST['login_id'];
		$password = $_POST['password'];

		$sql = "SELECT user_id, login_id, password, role FROM users WHERE login_id='$login_id' OR email='$login_id' OR username='$login_id' AND password='$password'";

		$result = $dbc->query($sql);
		
		if($row = $result->fetch_assoc()) {

			$_SESSION['user_id' ] = $row['user_id' ];

			if(isset($_POST['ajax'])) {echo '../home';}
			
			else {header("Location: ../home");}
			

		} else {
			if(isset($_POST['ajax'])) {
				header('HTTP/1.1 500 Incorrect login information');
		        header('Content-Type: application/json; charset=UTF-8');
		        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
		    } else {
		    	$error = 'incorrect password';
		    }
		}
	}
?>