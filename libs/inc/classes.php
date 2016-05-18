<?php 
class page_elements {

	public static function head($title) {
		$jqueryLink = "../libs/js/jquery-2.2.3.min.js";
		$cssLink = "../libs/css/style.css";
		$functionsLink = "../libs/js/functions.js";
		$transitionsLink = "../libs/js/transitions.js";
		?>

		<meta charset="UTF-8">
		<title><?php echo $title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" type="text/css" href="<?php echo $cssLink; ?>">
		<script src="<?php echo $jqueryLink ?>"></script>
		<script src="<?php echo $functionsLink ?>"></script>
		<script src="<?php echo $transitionsLink ?>"></script>
			
		<?php
	}

	public static function footer() {
		
		?>

		<!-- html content -->
		
		<?php
	}

	public static function nav() {
		
		?>
		
		<!-- html content -->
		
		<?php
	}

}
?>