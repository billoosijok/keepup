 <?php 
$libs = "../libs";
require "$libs/inc/classes.php";

// Initializing the page object to store the 
// dbc in a static variable along with info from the SESSION.
page::init($dbc);

// If the user accessing the page doesn't have a role of S/Student
// then index.php takes care of them.
if (page::$USER_ROLE != "S") {
	header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php 	// echoes out the <head> for the page
		page::head('Welcome ' . page::$USER_INFO->first_name);	
?>
</head>
<body>
	<?php page::nav(); // Navbar for the page ?>
	
	<main id="content">
	<?php 
	$selectedClass = page::$nav_selectedItem;
	$listOfClasses = page::$nav_listOfItems;

	// If there is a class in the URL and it's not one of
	// the listed, then someone put a wrong class id.
	if ($selectedClass AND !in_array($selectedClass, $listOfClasses)) {
		die("<p class='error'> You've entered an abandoned class :/ <a href='".$_SERVER['SCRIPT_NAME']."'>Go back</a></p>");
	
	} else {
		if(!$selectedClass) {
			// If no class was selected/specified then show 
			// everything.
			$classCondition = "1";
		} else {
			// Building a condition for sql dpending upon the selected
			// class.
			$classCondition = "`classes`.class_code = '".$selectedClass."'";
		}

	// This will grab the agendas due in three days but ordered by rate
	$stmt = $dbc->prepare("
		SELECT `ag`.id AS agenda_id,`ag`.title, `ag`.due_time, `ag`.due_date,`ag`.rate, `ag`.class_id, `classes`.id AS class_id, `classes`.class_name, `classes`.class_code, `user_class`.user_id, `user_class`.class_id, `agenda_category`.category
		FROM agendas AS ag 
		
		INNER JOIN agenda_category ON `ag`.category_id = `agenda_category`.category_id 
		
		INNER JOIN classes 
			ON `ag`.class_id = `classes`.id 
		
		INNER JOIN user_class 
			ON `user_class`.class_id = `classes`.id 
		
		LEFT OUTER JOIN user_agenda 
			ON `ag`.id = `user_agenda`.agenda_id 
			AND `user_class`.`user_id` = `user_agenda`.user_id
		
		WHERE `user_class`.user_id = ? 
			AND $classCondition 
			AND TIMESTAMP(`ag`.due_date, `ag`.due_time) > NOW() 
			AND TIMESTAMP(`ag`.due_date, `ag`.due_time) < NOW() + interval 7 day
			AND COALESCE(`user_agenda`.status, 1) != 0
		
		ORDER BY 
			CASE WHEN (`ag`.due_date <= CURDATE() + interval 3 day) THEN rate END DESC, due_date,
    		CASE WHEN (`ag`.due_date > CURDATE() + interval 3 day) THEN due_date END, rate DESC
    		");

	try {
		//Binding user id
		$stmt->bindParam(1, page::$USER_ID, PDO::PARAM_INT);

		// Executing both statements
		$stmt->execute();
		// $stmt2->execute();

		// Merging both query results.
		$resultSet = $stmt->fetchAll();
		
	} catch (exception $e) {
		echo "<p>" . $e->getMessage() . "</p>";
		die();
	}
	?>
		
	<?php 
	foreach ($resultSet as $agenda) {
		?>
		<div class="card-wrapper <?php echo strtolower($agenda->class_code)?>"> 
		<article class="card <?php echo $agenda->category ?>">
			<h2><?php echo strtoupper($agenda->class_code) . " - " . 
							$agenda->class_name?> 

				<form action="../libs/inc/delete-card.php" class="delete-card">
					<input type="hidden" name="card_id" value="<?php echo $agenda->agenda_id ?>">
					<input type="hidden" name="user_id" value="<?php echo page::$USER_ID ?>">
					<input type="submit" class="done" value="&#9851;">
				</form>

			</h2>
			<div class="content">
				<div class="main">
				<?php echo "<b>" . $agenda->title . "</b>" ?>
				is due 
				<?php 

					$now = new DateTime();
					$str = $agenda->due_date . ' ' . $agenda->due_time;
					$due = new DateTime($str);
					echo \PrettyDateTime\PrettyDateTime::parse($due, $now) ?></div>
			</div>
		</article>
		</div>
		<?php
		}
	}
	?>
	<div id="undo">Undo?</div>
	</main>
</body>
</html>

