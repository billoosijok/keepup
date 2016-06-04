<?php 
	require 'db_records.php';

	$DB_HOST 	 = 'localhost';
	$DB_DRIVER   = 'mysql';
	$DB_USERNAME = 'root';
	$DB_PASSWORD = '';
	$DB_NAME     = 'keepup';

	try {
		$dbc = new DB($DB_DRIVER, $DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

	} catch (exeption $e) {
		echo $e->getMessage();
		if(isset($_POST['ajax'])) {
			header('HTTP/1.1 500 Couldn\'t Connect to Database');
		    header('Content-Type: application/json; charset=UTF-8');

			die(json_encode(array('message' => 'Error Establishing Database Connection', 'code' => 1337)));
		} else {
			die("Error Establishing Database Connection");
		}
	}
?>