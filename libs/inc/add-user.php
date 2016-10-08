<?php 

if(isset($_POST['add-user-manually'])) {

	## Setting the empty values to NULL so that the DB barfs the 
	## instead of having to check each one 😆
	$role = ($_POST['role']) ? $_POST['role'] : NULL;
	$login_id = ($_POST['login_id']) ? $_POST['login_id'] : NULL;
	$first_name = ($_POST['first_name']) ? $_POST['first_name'] : NULL;
	$last_name = ($_POST['last_name']) ? $_POST['last_name'] : NULL;
	$username = ($_POST['username']) ? $_POST['username'] : NULL;
	$email = ($_POST['email']) ? $_POST['email'] : NULL;
	$password = ($_POST['pin']) ? $_POST['pin'] : NULL;
	
	try {
		## preparing the insertion, binding params to it, then executing.
		$stmt = $dbc->prepare("INSERT INTO `users` (id, first_name, last_name, login_id, username, password, email, role) VALUES ('',?,?,?,?,SHA2(?,256),?,?)");

		$stmt->bindParam(1, $first_name, PDO::PARAM_STR);
		$stmt->bindParam(2, $last_name, PDO::PARAM_STR);
		$stmt->bindParam(3, $login_id, PDO::PARAM_INT);
		$stmt->bindParam(4, $username, PDO::PARAM_STR);
		$stmt->bindParam(5, $password, PDO::PARAM_STR);
		$stmt->bindParam(6, $email);
		$stmt->bindParam(7, $role, PDO::PARAM_STR);

		$stmt->execute();

	} catch (exception $e) {
		// Setting the error in a different variable because
		if($role == 'S') {
			$studentError = $e->getMessage();
		} elseif($role == 'T') {
			$teacherError = $e->getMessage();
		}
	}

} elseif(isset($_POST['add-user-db'])) {

	// The role will tell us what kinda data are we gunna work on,
	// student or teacher.
	$role = $_POST['role'];
	
	// The uploaded file.
	$file = $_FILES['csv']['tmp_name'];

	// To make sure they actually submitted a file.
	if($file) {

		// Oppening ... 
		$file = fopen($file, 'r', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		// Getting the first line. aka the column names
		// --> note: getcsv parses csv data and returns an array of cell data
		$cols = fgetcsv($file);

		// mapUserColumns() matches up the names of the given columns
		// with the ones in the database.
		$colMap = mapUserColumns($cols);

		if(!isset($colMap['flag'])) {

			$queryClasses = $dbc->query("SELECT * FROM `classes`");
			
			$classesInDb = $queryClasses->fetchAll();

			$classesMap = [];
			foreach ($classesInDb as $row) {
				$classesMap[strtolower($row->class_code)] = $row->id;
				$classesMap[strtolower($row->class_name)] = $row->id;
			}
			
			$classes_position = $colMap['classes'];
			$classes = array_pop_key($colMap,'classes');

			$errorRows = [];

			$queryToAddUser = $dbc->prepare("INSERT INTO users (".implode(",",array_keys($colMap)).",role) VALUES (?,?,?,?,?,?,'$role')");
			
			$queryToAddUserToClass = $dbc->prepare("INSERT INTO user_class (class_id, user_id) VALUES (?,?)");

			while($row = fgetcsv($file) AND count($row)) {
				
				try {
					$userClasses = explode(",",array_pop_key($row, $classes_position));

					// Crypting the password.
					$row[$colMap['password']] = hash("sha256", $row[$colMap['password']]);

					$values = [];

					foreach ($colMap as $key => $index) {
						array_push($values, $row[$index]);
					}
					
					$queryToAddUser->execute($values);
					
					$user_id = $dbc->lastInsertId();
					
					foreach ($userClasses as $className) {
						$class_id = $classesMap[strtolower(trim($className))];
						$queryToAddUserToClass->execute([$class_id, $user_id]);
					}

					$success = "Data added to the database";

				} catch (exception $e) {
					array_push($errorRows, $row);
				}
			}

			if(sizeof($errorRows)) {
				// Things to do when there are errors.
			}
		} else {
			if($role == "T") {
				$teacherError = "Couldn't reconize the column " . $colMap['flag'] .".";
			} else {
				$studentError = "Couldn't reconize the column " . $colMap['flag'] .".";
			}
			
		}	
	} else {
		if($role == "T") {
			$teacherError = "Please choose a file.";
		} else {
			$studentError = "Please choose a file.";
		}
	}
}

function mapUserColumns($columnsArray) {
	$map = [];

	foreach ($columnsArray as $index => $name) {
		switch (true) {
			case preg_match('/([^a-z]|^)id(\b|[^a-z])/i', $name):
				$map['login_id'] = $index;
				break;
			case preg_match('/first(\S|\s|)name/i', $name):
				$map['first_name'] = $index;
				break;
			case preg_match('/last(\S|\s|)name/i', $name):
				$map['last_name'] = $index;
				break;
			case preg_match('/email/i', $name):
				$map['email'] = $index;
				break;
			case preg_match('/(pin|password)/i', $name):
				$map['password'] = $index;
				break;
			case preg_match('/((user\s|\Sname)|username)/i', $name):
				$map['username'] = $index;
				break;
			case preg_match('/classes/i', $name):
				$map['classes'] = $index;
				break;
			default: 
				$map['flag'] = $name;
				break;
		}
	}
	return $map;
}


function array_pop_key(&$array, $key) {
	$element = $array[$key];
	unset($array[$key]);
	return $element;
}