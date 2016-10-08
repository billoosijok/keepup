<?php 
require_once "db_connect.php";

if(!isset($_SESSION)) { session_start(); }

require 'PrettyDateTime.php';

class page {

	public static $USER_ID;
	public static $USER_ROLE;
	public static $USER_INFO;
	public static $DBC;

	public static $nav_selectedItem;
	public static $nav_listOfItems;

	public static function init(&$dbc) {
		if(isset($_GET['logout'])) {
			session_unset();
		
		} elseif (!isset($_SESSION['user_info'])) {
			header("Location: index.php");
		
		} else {
			self::$USER_INFO = $_SESSION['user_info'];
			self::$USER_ID = self::$USER_INFO->id;
			self::$USER_ROLE = self::$USER_INFO->role;
			self::$DBC = $dbc;
		}
	}

	public static function head($title) {
		
			$jqueryLink = "../libs/js/jquery-2.2.3.min.js";
			$cssLink = "../libs/css/style.css";
			$functionsLink = "../libs/js/functions.js";
			$transitionsLink = "../libs/js/transitions.js";
			$userJsLink = "../libs/js/user.js";
			$jqueryUILink = "../libs/js/jquery-ui.min.js";
		?>
			<meta charset="UTF-8">
			<title><?php echo $title; ?></title>
			<meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=1, user-scalable=no">
			
			<link rel="stylesheet" type="text/css" href="<?php echo $cssLink; ?>">
			<script src="<?php echo $jqueryLink ?>"></script>
			<script src="<?php echo $functionsLink ?>"></script>
			<script src="<?php echo $transitionsLink ?>"></script>
			<script src="<?php echo $userJsLink ?>"></script>
			<script src="<?php echo $jqueryUILink ?>"></script>

		<?php
	}

	public static function nav() {

		$sql = "SELECT `classes`.id,`classes`.class_name,`classes`.class_code, `user_class`.class_id FROM classes INNER JOIN user_class ON `classes`.id = `user_class`.class_id WHERE `user_class`.user_id=" . self::$USER_ID;

		$query = self::$DBC->query($sql);
		$classes = $query->fetchAll();

		self::$nav_listOfItems = [];

		if(isset($_GET['class'])) {
			self::$nav_selectedItem = $_GET['class'];
		} else {
			self::$nav_selectedItem = false;
		}
	?>
	<nav>
		<p class="name"><?php echo self::$USER_INFO->first_name ?></p>
		<div class="classes-list">
			<ul>
				<li class="class_name <?php if(!self::$nav_selectedItem){echo "active";} ?>"><a href='?'>All</a></li>

				<?php 
				foreach ($classes as $class) {
					if(self::$nav_selectedItem AND self::$nav_selectedItem == $class->class_code)	{
						$li_class = "active";
					} else {$li_class = "";}

					echo "<li class='class_name ".$li_class."''><a href='?class=" . strtolower($class->class_code) . "'>" . strtoupper($class->class_code) . "</a></li>";
					
					array_push(self::$nav_listOfItems,$class->class_code);
				}
				?>
			</ul>
		</div>
		<div>
			<a href="?logout">Logout</a></li>
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
}
?>