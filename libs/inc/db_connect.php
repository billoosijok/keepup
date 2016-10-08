<?php 
	require_once 'db_records.php';

	$DB_HOST 	 = 'localhost';
	$DB_DRIVER   = 'mysql';
	$DB_USERNAME = 'root';
	$DB_PASSWORD = '';
	$DB_NAME     = 'keepup';

	global $dbc;
	$dbc = new DB($DB_DRIVER, $DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

	
?>