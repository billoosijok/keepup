<?php $libs = "../libs";
require "$libs/inc/db_connect.php";
require "$libs/inc/classes.php";
page::init($dbc);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
		page::head('Administration Page'); 
		
		if (page::$USER_ROLE != "A") {
			header("Location: index.php");
		}
	?>
</head>
<body id="admin">
	<nav>
		<p class="name"><a href="index.php"><?php echo page::$USER_INFO->first_name ?></a></p>
		<div>
			<a href="?logout">Logout</a></li>
		</div>
	</nav>

<?php 
	if(isset($_GET['teachers'])) {
		$table = 'users';
		$sql = "SELECT id,login_id, first_name,last_name,email, username FROM $table WHERE role='T'";
		
		$columns = array(
				"login_id" => 'Teacher ID' , 
				'first_name' => "First Name", 
				'last_name' => "Last Name" 
			);

	} elseif (isset($_GET['students'])) {
		$table = 'users';
		$sql = "SELECT id, login_id, first_name,last_name,email,login_id, username FROM $table WHERE role='S'";

		$columns = array(
				"login_id" => 'Student ID' , 
				'first_name' => "First Name", 
				'last_name' => "Last Name" 
			);

	} else {
		header("Location: admin.php");
	}
?>
	<main id="content" class="edit">
		<div id="sub-nav">
			<a class="button-link" href='admin.php'>Main</a>
			<label>Search Table: <input type="text" name="search" id="search"></label>
		</div>
		<script type="text/javascript" src="../libs/js/admin-table.js"></script>
	
		<?php 
		$dbc->echoTableOfRecords($sql, $columns,"recordsTable", $table); 
		?>
		<script type="text/javascript">
			$(function() {
				var RecordsTable = new SmartTable("#recordsTable");
				RecordsTable.activateSearch("#search");
				RecordsTable.activateEdit();
			});

		</script>
	</main>
</body>
</html>