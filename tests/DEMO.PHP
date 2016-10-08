<?php 

############################################################################
############################################################################

elseif (isset($_GET['classes'])) {
		$table = 'classes';
		$sql = "SELECT * FROM $table";

		$columns = array(
			'class_name' => "Class Name", 
			"class_code" => 'Class Code'
			);
	
	}