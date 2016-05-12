<?php
	session_start();

	require 'db_connect.php';	

	if(isset($_POST['login_id']) AND isset($_POST['password'])) {

		$login_id = $_POST['login_id'];
		$password = $_POST['password'];

		$sql = "SELECT user_id, login_id, password FROM users WHERE login_id='$login_id' OR email='$login_id' OR username='$login_id' AND password='$password'";

		$result = $dbc->query($sql);
		
		if($row = $result->fetch_assoc()) {
			$_SESSION['user_id' ] = $row['user_id' ];
			
		} else {
			header('HTTP/1.1 500 Internal Server Booboo');
	        header('Content-Type: application/json; charset=UTF-8');
	        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
		}

	} else {
		header($_SERVER['SERVER_PROTOCOL']. " 404 Not Found", true, 404);
		echo "NOT FOUND";
	}


 ?>