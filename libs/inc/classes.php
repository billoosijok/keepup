<?php 
session_start();

require 'PrettyDateTime.php';

class page {

	public static $USER_ID;
	public static $USER_ROLE;
	public static $USER_INFO;

	public static function head($title) {
		
		if(isset($_GET['logout'])) {
			session_unset();
		
		} elseif (!isset($_SESSION['user_info'])) {
			header("Location: index.php");
		
		} else {
			self::$USER_INFO = $_SESSION['user_info'];
			self::$USER_ID = self::$USER_INFO->user_id;
			self::$USER_ROLE = self::$USER_INFO->role;

			$jqueryLink = "../libs/js/jquery-2.2.3.min.js";
			$cssLink = "../libs/css/style.css";
			$functionsLink = "../libs/js/functions.js";
			$transitionsLink = "../libs/js/transitions.js";
			$userJsLink = "../libs/js/user.js";
		?>
			<meta charset="UTF-8">
			<title><?php echo $title; ?></title>
			<meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=1, user-scalable=no">
			
			<link rel="stylesheet" type="text/css" href="<?php echo $cssLink; ?>">
			<script src="<?php echo $jqueryLink ?>"></script>
			<script src="<?php echo $functionsLink ?>"></script>
			<script src="<?php echo $transitionsLink ?>"></script>
			<script src="<?php echo $userJsLink ?>"></script>
		<?php
		}	
	}

	public static function footer() {
		
		?>

		<!-- html content -->
		
		<?php
	}

	public static function nav() {
		
		?>
		<nav>
			<div id="menu-toggle">
			<p><?php echo self::$USER_INFO->first_name ?></p>
			<ul id="menu">
				<li><a href="?logout">Logout</a></li>
			</ul>
			</div>
		</nav>
		
		<?php
	}
}

class Format {

	public static function due_date($sqlDate) {
		$timestamp = strtotime($sqlDate);
		$nextWeek = time() + (7 * 24 * 60 * 60);

		// This week
		if($timestamp < $nextWeek && $timestamp > (time() - (24 * 60 * 60))) {
			$today = date('j', time());
			$tomorrow = date('j', time() + (60 * 60 * 24));
			$dayOfTimestamp = date('j', $timestamp);
			
			switch ($dayOfTimestamp) {
				case $today: return "Today";
				case $tomorrow: return "Tomorrow";
				default: return date("l", $timestamp);
			}
			
		} else {
			return date('M j\, Y', $timestamp);
		}
	}

	public static function sqlDate($jqDate) {

		$jqDate = explode('/', $jqDate);
		
		$year = array_pop($jqDate);

		array_unshift($jqDate, $year);

		$sqlDate = implode("-",$jqDate);
		
		return $sqlDate;
	}

	public function days_until($sqlDate) {

	}
}
?>