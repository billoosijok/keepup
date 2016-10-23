<?php 

# This is when class was added by filling out fields.
if(isset($_POST['add-class-manually'])) {
	// Getting the fields.
	$class_name = $_POST['class_name'];
	$class_code = $_POST['class_code'];

	// Preping the insert statment.
	$query = $dbc->prepare("INSERT INTO classes (class_name,class_code) VALUES (?,?)");

	try{
		// Getting the class_code ready for the database by 
		// replacing any underscores and spaces in the class code,
		// then converting it to lower case. 
		$class_code = preg_replace('/(\s+|_+)/', '', $class_code);
		$class_code = strtolower($class_code);

		// Binding and Executing ...
		$query->execute([$class_name, $class_code]);
	
	} catch (exception $e) {
		// classError is echoed out in the page when it is set.
		$classError = "Something went wrong, please check your data.";
	}

# This is when class was added by uploading a file.
} elseif(isset($_POST['add-class-db'])) {

	// Retrieving the temporary directory of the file.
	$file = $_FILES['csv']['tmp_name'];

	# 👇If nothing was submitted.
	if(!$file) {
		$classError = "Please choose a file";
	} else {
		// Opening the file ..
		$file = fopen($file, 'r');

		// Reading the first line which will be the columns.
		$cols = fgetcsv($file);

		// mapClassesColumns() takes the columns from the csv
		// and indexs their positions, to know which column in
		// the file corresponds to which column in the db.
		$colMap = mapClassesColumns($cols);
		
		$errorRows = []; // To accumlate it with errors

		# If this was set then there is an unrecognized 
		# column name in the csv.
		if(isset($colMap['flag'])) {
			$classError = "Couldn't reconize the column " . $colMap['flag'] .".";
		} else {
			// Preparing it by imploding the returned array from 
			// the mapping function.
			$query = $dbc->prepare("INSERT INTO classes (".implode(',',array_keys($colMap)).") VALUES (?,?)");

			// getting the rows
			while($row = fgetcsv($file) AND count($row)) {
			
				try {
					// This will contain the cells in each row.
					$values = [];
					
					// Going thru each cell in the loop. 
					// Note that the column name in the ColMap
					// is associated with its position in the
					// csv. That position is used below to index
					// data and append it to the query in the same 
					// order as the columns are ordered in the 
					// prepared statement.
					foreach ($colMap as $column => $index) {
						# To prettify the class_code for the DB
						if($column == 'class_code') {
							$row[$index] = preg_replace('/(\s+|_+)/', '', $row[$index]);
							$row[$index] = strtolower($row[$index]);
						}
						// Pushing the cellData the the array of values.
						array_push($values, $row[$index]);
					}

					// Execute can bind params by taking them in 
					// an argument as an array.
					$query->execute($values);	

					$success = "Data added to the database";	
				} catch (PDOException $e) {
					array_push($errorRows, $row);
				}
			}

			# If there are any errors.
			if(sizeof($errorRows)) {
				// This will be displayed in the page when it is set
				// which is what we're doing right here.
				$classError = "Couldn't insert the following rows, because they already exist in the database: <ul>";

				// Going thru each errored row and displaying it 
				// as a list item
				foreach ($errorRows as $row) {
					$classError .= "<li><pre>" . implode('|  ', $row) . "</pre></li>";
				}
				$classError .= "</ul>"; 
			}

		} 
	} 
}

function mapClassesColumns($columnsArray) {
	// This function maps out the position of the columns
	// and returns an accosiative array of pairs 
	// [column_name => position]

	$map = [];
	foreach ($columnsArray as $index => $name) {
		switch (true) {
			case preg_match('/class(\s|\S|)code/i', $name):
				$map['class_code'] = $index;
				break;
			case preg_match('/class(\S|\s|)name/i', $name):
				$map['class_name'] = $index;
				break;
			default: 
				$map['flag'] = $name;
				break;
		}
	}
	return $map;
}