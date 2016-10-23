<?php 
$libs = "../libs";
require_once "$libs/inc/classes.php";

// This initializes the dbc to the page along with 
// SESSION and other essential stuff. 
// class Page{} is defined in ~/libs/classes.php.
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
	<?php 	

	// Takes the title and echoes out the <head> for the page 
	page::head('Welcome ' . page::$USER_INFO->first_name);	
	
	?>
	<script src="<?php echo "$libs/js/student.js"; ?>"></script>
</head>
<body>
	<?php page::nav(); // Navbar for the page ?>
	
	<main id="content">
	<?php 
	$selectedClass = page::$nav_selectedItem;
	$listOfClasses = page::$nav_listOfItems;

	if(!$selectedClass) {
		// If no class was selected/specified then show 
		// everything.
		$classCondition = "1";
	} else {
		// Building a condition for sql dpending upon the selected
		// class.
		$classCondition = "`classes`.class_code = '".$selectedClass."'";
	}

	// This query will grab the agendas due in three days but ordered by rate for
	// the ones that are due in less than two days, then by date for the rest, up till
	// 7 days from the current day.
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

		// Executing ...
		$stmt->execute();

		// This will be all the cards. 
		// Note: fetchAll returns an array of rows as objects.
		$resultSet = $stmt->fetchAll();
		
	} catch (exception $e) {
		die("<p>Something went wrong. Please check back later.</p>");
	}
	if (!$resultSet) {
		echo "<p class='none'>Nothing to do for now :)</p>";
	} else {
		foreach ($resultSet as $agenda) {
		?>
		<div class="card-wrapper <?php echo strtolower($agenda->class_code);?>"> 
			<article class="card <?php echo $agenda->category ?>">
				<h2><?php echo strtoupper($agenda->class_code) . " - " . 
							$agenda->class_name?> 
					<!-- this form is needed to delete a card -->
					<form action="../libs/inc/delete-card.php" class="delete-card">
						<input type="hidden" name="card_id" value="<?php echo $agenda->agenda_id ?>">
						<input type="hidden" name="user_id" value="<?php echo page::$USER_ID ?>">
						<input type="submit" class="done" value="&#9851;">
					</form>
				</h2>

				<div class="content">
					<div class="main">
					  <?php 

						$now = new DateTime();
						// Building a DateTime string
						// to use it to create a new DateTime object
						$due = $agenda->due_date . ' ' . $agenda->due_time;
						$due_date = new DateTime($due);
						
						// {func: PrettyDateTime} takes a date and the current date and returns the differece
						// in a pretty format.
						echo "<b>" . $agenda->title . "</b> is due " . PrettyDateTime::parse($due_date, $now);
						
					  ?>
					</div>
				</div>
			</article>
		</div>
	<?php
	
		}
	}
	
	
	?>
	<!-- This is a hidden div that shows up when a card is deleted -->
	<div id="undo">Undo?</div>

</main>	</body>	</html>