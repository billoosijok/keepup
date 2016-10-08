<?php 
require 'db_connect.php';

if (isset($_POST)) {
	var_dump($_POST);
	$table = $_POST['table'];
	$columns = $dbc->getColumnNames($table);

	$rowId = $_POST['id'];
	
	$validColumns = [];

	foreach ($_POST as $key => $value) {
		if(in_array($key, $columns)) {
			$validColumns[$key] = $value;
		} 
	}

	$dataToPrepare = [];
	foreach ($validColumns as $key => $value) {
		if($key != 'id') {
			array_push($dataToPrepare, "`$key` = :$key");
		}
	}
	$dataToPrepare = implode(",", $dataToPrepare);
	
	$sql = "UPDATE $table SET $dataToPrepare WHERE id= :id";
	try {
		$stmt = $dbc->prepare($sql);
		$stmt->bindParam(":id", $rowId);

		foreach ($validColumns as $key => &$value) {
			if($key != 'id') {
				$stmt->bindParam(":$key", $value);
			}
		}
		$stmt->execute();
	} catch (exception $e) {

	} 
}
