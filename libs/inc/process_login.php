<?php
require 'db_connect.php';	

	if(!isset($_POST['login_id']) AND !isset($_POST['password'])) {
		header($_SERVER['SERVER_PROTOCOL']. " 404 Not Found", true, 403);
	
	} else {

		$login_id = $_POST['login_id'];
		$password = $_POST['password'];

		$sql = "SELECT * FROM users WHERE login_id= :login OR email= :login OR username= :login AND password= :pass";
		
		try {
			$statement = $dbc->prepare($sql);

			$statement->bindParam(":login", $login_id);
			$statement->bindParam(":pass", $password);
			
			$statement->execute();

		} catch (exeption $e) {
			$e->getMessage();
		}

		if($row = $statement->fetch()) {
			$_SESSION['user_info'] = $row;

			if(isset($_POST['ajax'])) {echo '../home';}
			
			else {header("Location: ../home");}
			

		} else {
			if(isset($_POST['ajax'])) {
				header('HTTP/1.1 500 Incorrect login information');
		        header('Content-Type: application/json; charset=UTF-8');
		        die(json_encode(array('message' => 'ERRORss', 'code' => 1337)));
		    } else {
		    	$error = 'incorrect password';
		    }
		}
	}
?>