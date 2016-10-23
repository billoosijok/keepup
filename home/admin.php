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
	page::head('Administration Dashboard'); 
	?>
</head>
<body id="admin">
	<nav>
		<p class="name"><a href="index.php"><?php echo page::$USER_INFO->first_name ?></a></p>
		
		<div>	<a href="?logout">Logout</a>	</div>
	</nav>
	<main id="content" class="admin main">
	<?php 

	// class OptionSet{} creates an option set that can have multiple
	// options.
	$EditRecords = new OptionSet("Control Records");

	// Method addOption() adds an option to the option set.
	$EditRecords->addOption("Students", "Edit students' information", "admin-edit-panel.php?students");
	$EditRecords->addOption("Teachers", "Edit teachers' information", "admin-edit-panel.php?teachers");
	
	// Uncomment this line👇 to add an option of editing classes
	/* $EditRecords->addOption("Classes", "Edit classes' information", "admin-edit-panel.php?classes"); */
	
	// This echoes out the optionset
	$EditRecords->EchoOut();

	// And another optionset
	$Tools = new OptionSet("Tools");
	$Tools->addOption("Registeration", "Add students, teachers and classes", "admin-add-panel.php");
	$Tools->EchoOut();
	?> 
</main> 
</body></html>

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