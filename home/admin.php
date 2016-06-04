<?php $libs = "../libs";
require "$libs/inc/db_connect.php";
require "$libs/inc/classes.php";
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
<body>
	<?php page::nav(); ?>
	<aside id="sidebar">
		<ul>
			<li>Edit records</li>
			<li>Add records</li>
		</ul>
	</aside>
</body>
</html>