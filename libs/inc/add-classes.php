<?php 

if(isset($_POST['add-class-manually'])) {
	$class_name = $_POST['class_name'];
	$class_code = $_POST['class_code'];

	$query = $dbc->prepare("INSERT INTO classes (class_name,class_code) VALUES (?,?)");

	try{
		$class_code = preg_replace('/(\s+|_+)/', '', $class_code);
		$class_code = strtolower($class_code);
		$query->execute([$class_name, $class_code]);
	
	} catch (exception $e) {
		echo $e->getMessage();
	}

} elseif(isset($_POST['add-class-db'])) {

	$file = $_FILES['csv']['tmp_name'];

	if($file) {
		$file = fopen($file, 'r');

		$cols = fgetcsv($file);

		$colMap = mapClassesColumns($cols);

		$errorRows = [];

		if(!isset($colMap['flag'])) {

			$query = $dbc->prepare("INSERT INTO classes (".implode(',',array_keys($colMap)).") VALUES (?,?)");


			while($row = fgetcsv($file)) {
			
				try {
					$values = [];
					
					foreach ($colMap as $key => $index) {
						if($key == 'class_code') {
							$row[$index] = preg_replace('/(\s+|_)/', '', $row[$index]);
							echo $row[$index];
							$row[$index] = strtolower($row[$index]);
						}
						array_push($values, $row[$index]);
					}

					$query->execute($values);	

					$success = "Data added to the database";	
				} catch (PDOException $e) {
					array_push($errorRows, $row);
				}
			}

			if(sizeof($errorRows)) {
				
				$classError = "Couldn't insert the following rows, because they already exist in the database: <ul>";
				foreach ($errorRows as $row) {
					$classError .= "<li><pre>" . implode('|  ', $row) . "</pre></li>";
				}
				$classError .= "</ul>";
			}

		} else {
			$classError = "Couldn't reconize the column " . $colMap['flag'] .".";
		}
	} else {
		$classError = "Please choose a file";
	}
}

function mapClassesColumns($columnsArray) {
	$map = [];

	foreach ($columnsArray as $index => $name) {
		switch (true) {
			case preg_match('/class(\s|\S|)code/i', $name):
				$map['class_code'] = $index;
				break;
			case preg_match('/class(\S|\s|)name/i', $name):
				$map['class_name'] = $index;
				break;
			// default: 
			// 	$map['flag'] = $name;
			// 	break;
		}
	}
	return $map;
}