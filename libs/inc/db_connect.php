<?php 
	$dbc = new mysqli("localhost", "root", "", "keepup") OR die('Error connecting to db');

	if ($dbc->connect_error) {
		header('HTTP/1.1 500 Internal Server Booboo');
	    header('Content-Type: application/json; charset=UTF-8');
		die(json_encode(array('message' => 'error establishing database connection', 'code' => 1337)));
	}
?>