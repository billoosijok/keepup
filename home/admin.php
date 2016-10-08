<?php $libs = "../libs";
require_once "$libs/inc/db_connect.php";
require_once "$libs/inc/classes.php";
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
			<a href="?logout">Logout</a>
		</div>
	</nav>
	<main id="content" class="admin main">
	<?php 

	$EditRecords = new OptionSet("Control Records");

	$EditRecords->addOption("Students", "Edit students' information", "admin-edit-panel.php?students");
	$EditRecords->addOption("Teachers", "Edit teachers' information", "admin-edit-panel.php?teachers");
	
	// $EditRecords->addOption("Classes", "Edit classes' information", "admin-edit-panel.php?classes");
	
	$EditRecords->EchoOut();

	
	$Tools = new OptionSet("Tools");

	$Tools->addOption("Register Users", "Add students and teachers", "admin-add-panel.php");

	$Tools->EchoOut();
	?> 
</main>
</body>
</html>

<?php 

class OptionSet {
	var $title;
	private $options = [];

	function __construct($title) {
		$this->title = $title;
	}

	public function EchoOut() {
		?>
		<div class="option-set">
		<p class="group-name"><?php echo $this->title; ?></p>
		<?php
		foreach ($this->options as $option) {
			echo $option;
		}

		?>
		</div>
		<?php
	}

	function addOption($title, $subtitle, $link) {
		$option = 
		"<a href='$link' class='option'>
			<div class='option-title'>$title</div>
			<div class='option-subtitle'>$subtitle</div>
		</a>";

		array_push($this->options, $option);
	}
}


 ?>