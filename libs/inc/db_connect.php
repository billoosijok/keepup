<?php 
	$dbc = new mysqli("localhost", "root", "", "keepup");

	if ($dbc->connect_error) {
		if(isset($_POST['ajax'])) {
			header('HTTP/1.1 500 Internal Server Booboo');
		    header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(array('message' => 'error establishing database connection', 'code' => 1337)));
		} else {
			die("error establishing database connection");
		}
		
	}
?>