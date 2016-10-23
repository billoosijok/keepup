<?php $libs = "../libs";
require_once "$libs/inc/db_connect.php";
require_once "$libs/inc/classes.php";

// This initializes the dbc to the page along with 
// SESSION and other essential stuff. 
// class Page{} is defined in ~/libs/classes.php.
page::init($dbc);

// If the user accessing the page doesn't have a role of A/Admin
// then index.php takes care of them.
if (page::$USER_ROLE != "A") {
	header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
	// Takes the title and echoes out the <head> for the page
	page::head('Control Records'); 
	?>
	<script type="text/javascript" src="<?php echo "$libs/js/smartTable.js"; ?>"></script>
</head>

<body id="admin">
	<nav>
		<p class="name"><a href="index.php"><?php echo page::$USER_INFO->first_name ?></a></p>

		<div>  <a href="?logout">Logout</a>	</div>
	</nav>

	<main id="content" class="edit">
		<div id="sub-nav">
			<a class="button-link" href='admin.php'>Main</a>
			<label>Search Table: <input type="text" name="search" id="search"></label>
		</div>

	<?php 
	// Initializing variables based on _GET request
	// that will be used to creat a table below
	if(isset($_GET['teachers'])) {
		$table = 'users';
		$sql = "SELECT id,login_id, first_name,last_name,email, username FROM $table WHERE role='T'";
		
		$columnsWanted = array(
				"login_id" => 'Teacher ID' , 
				'first_name' => "First Name", 
				'last_name' => "Last Name" 
			);

	} elseif (isset($_GET['students'])) {
		$table = 'users';
		$sql = "SELECT id, login_id, first_name,last_name,email,login_id, username FROM $table WHERE role='S'";

		$columnsWanted = array(
				"login_id" => 'Student ID' , 
				'first_name' => "First Name", 
				'last_name' => "Last Name"
			);

	} 
	// Uncomment this block to have a fully functionl
	// table with class records. You can add '?classes' to
	// url or uncomment line 42 in admin.php
	// 👇
 /* elseif (isset($_GET['classes'])) {
		$table = 'classes';
		$sql = "SELECT * FROM $table";

		$columnsWanted = array(
			'class_name' => "Class Name", 
			"class_code" => 'Class Code'
			);
	
	} */
	else {
		die("Unrecognized action for link: " . $_SERVER['REQUEST_URI'] . ":/ .. <a href='admin.php'>Go back</a>");
	}
?>
		<?php 
		// $dbc is an instance of class DB{}. It has 
		// a method echoTableOfRecords(). It takes
		// an sql query, the name of the wanted columns,
		// tableId in DOM, and the name of the table in DB.
		$dbc->echoTableOfRecords($sql, $columnsWanted,"recordsTable", $table); 
		?>
		<script type="text/javascript">
			$(function() {
				// SmatTable() is a class
				// that takes tableId and can makes it 
				// searchable and editable.
				var RecordsTable = new SmartTable("#recordsTable");
				RecordsTable.activateSearch("#search");
				RecordsTable.activateEdit();
			});
		</script>
	</main>
</body></html>