<?php 
/* This script updates a record updated by the admin table. */
require_once 'db_connect.php';

if (isset($_POST)) {
	// var_dump($_POST);

	// This is the table that will be modified.
	$table = $_POST['table'];

	// Thi method gets the names of all the columns of a cretain 
	// table in the db.
	$columns = $dbc->getColumnNames($table);

	// The row to update ... 
	$rowId = $_POST['id'];
	
	// Building an array of that contains only the POSTed fields
	// whose column exist in db table.
	$validColumns = [];
	foreach ($_POST as $key => $value) {
		// if the key is in the array of the columns then it
		// exists in the db. i.e it's valid 👏
		if(in_array($key, $columns)) {
			$validColumns[$key] = $value;
		} 
	}

	## Now that we have the valid column names, we can totally
	## implode them and use then in the sql stmt.
	## But now we need a corresponding set of values.
	
	// This will populate the sql with values-to-insert which then
	// will be prepared. That's why we add "column = 
	// NULLIF(:column,'')". This function is needed for the UNIQUE
	// Columns because if the field was empty then it will be stored
	// as an empty string in the db. And any other empty field/string
	// will not be accepted.
	$dataToPrepare = [];
	foreach ($validColumns as $key => $value) {
		if($key != 'id') {
			array_push($dataToPrepare, "`$key` = NULLIF(:$key, '')");
		}
	}
	$dataToPrepare = implode(",", $dataToPrepare);
	
	$sql = "UPDATE $table SET $dataToPrepare WHERE id= :id";
	try {
		$stmt = $dbc->prepare($sql);
		$stmt->bindParam(":id", $rowId);

		// Binding all the params
		foreach ($validColumns as $key => &$value) {
			if($key != 'id') {
				$stmt->bindParam(":$key", $value);
			}
		}
		$stmt->execute();
	} catch (exception $e) {
		echo $e->getMessage();
	} 
}
