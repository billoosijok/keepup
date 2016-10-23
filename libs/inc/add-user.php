<?php 

if(isset($_POST['add-user-manually'])) {

	## Setting the empty values to NULL so that the DB barfs  
	## (when the column doesn't allow NULL). That's instead of 
	## having to check each one 😆

	$role = ($_POST['role']) ? $_POST['role'] : NULL;
	$login_id = ($_POST['login_id']) ? $_POST['login_id'] : NULL;
	$first_name = ($_POST['first_name']) ? $_POST['first_name'] : NULL;
	$last_name = ($_POST['last_name']) ? $_POST['last_name'] : NULL;
	$username = ($_POST['username']) ? $_POST['username'] : NULL;
	$email = ($_POST['email']) ? $_POST['email'] : NULL;
	$password = ($_POST['pin']) ? $_POST['pin'] : NULL;
	$classes = ($_POST['classes']) ? $_POST['classes'] : NULL;
	
	try {

		// Querying for all the existing classes in the db.
		// That was we can connect students with existing
		// classes.
		$queryClasses = $dbc->query("SELECT * FROM `classes`");
		
		// fetching all the classes rows from the result set.
		$classesInDb = $queryClasses->fetchAll();

		// To fill it up with with existing classes.
		$classesMap = [];

		// Filling up the array with indexes of classes
		// in a form of [class_code => row_id, class_name=>
		// row_id]. That way when going thru the classes
		// cell for each student, we can use the name or code 
		// of the class as a sub script on this array to get 
		// the id of the class in the db.
		foreach ($classesInDb as $row) {
			$classesMap[strtolower($row->class_code)] = $row->id;
			$classesMap[strtolower($row->class_name)] = $row->id;
		}

		## preparing the insertion, binding params to it, then 
		## executing.
		$addUser = $dbc->prepare("INSERT INTO `users` (id, first_name, last_name, login_id, username, password, email, role) VALUES ('',?,?,?,?,SHA2(?,256),?,?)");
		$addUserClass = $dbc->prepare("INSERT INTO user_class (class_id, user_id) VALUES (?,?)");

		$addUser->bindParam(1, $first_name, PDO::PARAM_STR);
		$addUser->bindParam(2, $last_name, PDO::PARAM_STR);
		$addUser->bindParam(3, $login_id, PDO::PARAM_INT);
		$addUser->bindParam(4, $username);
		$addUser->bindParam(5, $password, PDO::PARAM_STR);
		$addUser->bindParam(6, $email);
		$addUser->bindParam(7, $role, PDO::PARAM_STR);

		$addUser->execute();

		// Getting the id of the inserted user. To use it to add
		// a connection between it and the classes he/she is 
		// enrolled into (in the user_class table).
		$userId = $dbc->lastInsertId();
		// Array of the classes that the user is enrolled into.
		$listOfClasses = explode(',', $classes);

		// Getting the class id in the db by using the class name
		// as an index to the classesMap.
		foreach ($listOfClasses as $className) {
			$class_id = $classesMap[strtolower(trim($className))];
			$addUserClass->execute([$class_id, $user_id]);
		}

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

	// 👇 when no file is submitted.
	if(!$file) {
		if($role == "T") {
			$teacherError = "Please choose a file.";
		} else {
			$studentError = "Please choose a file.";
		}
	} else {
		// Oppening ... 
		// Passing two optional params. The first ignores
		// '\n' at the end of the line. The second (obviously)
		// skips empty lines.
		$file = fopen($file, 'r', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		// Getting the first line which has the column names
		$cols = fgetcsv($file);
		// 👆--> note: getcsv() parses csv data and returns an array 
		// of data in the row

		// mapUserColumns() takes the columns from the csv
		// and indexs their positions, to know which column in
		// the file corresponds to which column in the db.
		$colMap = mapUserColumns($cols);

		# If this was set then there is an unrecognized 
		# column in the csv.
		if(isset($colMap['flag'])) {
			if($role == "T") {
				$teacherError = "Couldn't reconize the column " . $colMap['flag'] .".";
			} else {
				$studentError = "Couldn't reconize the column " . $colMap['flag'] .".";
			}
		} else {

			// Querying for all the existing classes in the db.
			// That was we can connect students with existing
			// classes.
			$queryClasses = $dbc->query("SELECT * FROM `classes`");
			
			// fetching all the classes rows from the result set.
			$classesInDb = $queryClasses->fetchAll();

			// To fill it up with with existing classes.
			$classesMap = [];

			// Filling up the array with indexes of classes
			// in a form of [class_code => row_id, class_name=>
			// row_id]. That way when going thru the classes
			// cell for each student, we can use the name or code 
			// of the class as a sub script on this array to get 
			// the id of the class in the db.
			foreach ($classesInDb as $row) {
				$classesMap[strtolower($row->class_code)] = $row->id;
				$classesMap[strtolower($row->class_name)] = $row->id;
			}

			// To get the position of thr 'classes' column in 
			// the csv which was indexed in the mapping func 😏.
			$classes_position = $colMap['classes'];
			
			// Now that we got the column position down, we get
			// rid of it so we can join the rest of columns to use
			// in the sql stmt.
			// This function is defined below. It basically pops 
			// and element from an array with a specific key.
			array_pop_key($colMap,'classes');

			// Becz students and classes are different tables. So 
			// we're gunna have different queries. One to insert
			// a student and one to insert a connecting-record
			// to connect the student with the class.
			$queryToAddUser = $dbc->prepare("INSERT INTO users (".implode(",",array_keys($colMap)).",role) VALUES (?,?,?,?,?,'$role')");
			$queryToConnectUserToClass = $dbc->prepare("INSERT INTO user_class (class_id, user_id) VALUES (?,?)");

			$errorRows = [];

			// count($row) will evaluate to 0/false if the row 
			// if empty. 👎
			while($row = fgetcsv($file) AND count($row)) {
				
				try {
					// because array_pop_key() returns the popped 
					// elemnt (which in this case will be the 
					// cell of classes),it is being
					// exploded into an array, which will be
					// the array of classes in that cell.
					$userClasses = explode(",",array_pop_key($row, $classes_position));

					// Crypting the password 🔏.
					$row[$colMap['password']] = hash("sha256", $row[$colMap['password']]);

					// This will collect the row cells.
					$values = [];

					// Using the index in the colMap to 
					// append data in the same order as 
					// they were mapped. That way the query
					// will be built correctly.
					foreach ($colMap as $key => $index) {
						array_push($values, $row[$index]);
					}
					// And 👍
					$queryToAddUser->execute($values);
					
					// This will be the id of the inserted row/user
					// and will be used to insert a row in the 
					// user_class table.
					$user_id = $dbc->lastInsertId();
					

					foreach ($userClasses as $className) {
						$class_id = $classesMap[strtolower(trim($className))];
						$queryToAddUserToClass->execute([$class_id, $user_id]);
					}

					$success = "Data added to the database";

				} catch (exception $e) {
					array_push($errorRows, $row);
					if($role == "T") {
						$teacherError = "Something has gone wrong.";
					} else {
						$studentError = "Something has gone wrong.";
					}
				}
			}

			if(sizeof($errorRows)) {

				if($role == "T") {
					// This will be displayed in the page when it is set
					// which is what we're doing right here.
					$teacherError = "Couldn't insert the following rows, because they already exist in the database: <ul>";

					// Going thru each errored row and displaying it 
					// as a list item
					foreach ($errorRows as $row) {
						$teacherError .= "<li><pre>" . implode('|  ', $row) . "</pre></li>";
					}
					$teacherError .= "</ul>"; 
				
				} else {
					// This will be displayed in the page when it is set
					// which is what we're doing right here.
					$studentError = "Couldn't insert the following rows, because they already exist in the database: <ul>";

					// Going thru each errored row and displaying it 
					// as a list item
					foreach ($errorRows as $row) {
						$studentError .= "<li><pre>" . implode('|  ', $row) . "</pre></li>";
					}
					$studentError .= "</ul>";
				}
			}
		} 	
	}
}

function mapUserColumns($columnsArray) {
	// This function maps out the position of the columns
	// in the csv and returns an accosiative array of pairs 
	// [column_name => position]
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