<?php 
	
	$file = fopen("Workbook1.csv", "r");
	$sql = "";

	$line = fgetcsv($file);

	$values = ""
	foreach ($line as $value) {
		# code...
	}

	$insert = "INSERT INTO students VALUES (" . $values . ")";
	while ($line = fgetcsv($file)) {
		echo "<pre>";
		var_dump($line);
		echo "</pre>";
	}

	fclose($file);
	
?>