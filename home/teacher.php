<?php 
$libs = "../libs";
require "$libs/inc/classes.php";

// This initializes the dbc to the page along with 
// SESSION and other essential stuff. 
// class Page{} is defined in ~/libs/classes.php.
page::init($dbc);

// If the user accessing the page doesn't have a role of T/Teacher
// then index.php takes care of him/her.
if (page::$USER_ROLE != "T") {
	header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
	// Takes the title and echoes out the <head> for the page 
	// $USER_INFO is a static property that was initialized in init() above
	// it holds an object that contains the logged-in-user's information.
	page::head('Welcome '. page::$USER_INFO->first_name); 
	
	?>
	<script src="<?php echo "$libs/js/validate_agenda_entry.js"; ?>"></script>
	<script src="<?php echo "$libs/js/teacher.js"; ?>"></script>
</head>

<body>
	<nav>
		<p class="name"><?php echo page::$USER_INFO->first_name ?></p>
		<div>	<a href="?logout">Logout</a>	</div>
 	</nav>

	<main id="content">
		<div id="addToggle">+</div><span class="label">Add new agendas</span>
		
		<form id="addAgenda">
			<div class="row">
				<div class="field half">
					<label for="categoryField">Category: </label>
					<select name="category" id="categoryField">
						<?php 
						
						// Getting the categories from the db.
						$stmt = $dbc->query("SELECT * FROM `agenda_category`");
						$categories = $stmt->fetchAll();
						foreach($categories as $category) {
							echo "<option value='" . $category->category_id ."'>" . ucfirst($category->category) . "</option>";
						}
						
						?>
					</select>
				</div>

				<div class="field half">
					<label for="classField">Class: </label>
					<select name="class" id="classField">
						<?php 
						$stmt = $dbc->query("SELECT * FROM `classes` INNER JOIN user_class ON `classes`.id = `user_class`.class_id WHERE `user_class`.user_id =".page::$USER_ID);
						$classes = $stmt->fetchAll();

						foreach($classes as $class) {
							echo "<option value='" . $class->id ."'>" . ucfirst($class->class_name) . "</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="field half">
					<label for="titleField">Title: </label>
					<input type="text" name="title" id="titleField">
					
					<div class="hidden error"></div>
				</div>
				
				<div class="field half">
					<label for="rateField">Rate: </label>
					<select name="rate" id="rateField">
						<?php 
						// Options from 1 to 5
						for ($i=1; $i <= 5; $i++) { 
							echo "<option value='$i'>$i</option>";
						} 
						?>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="field half">
					<label for="dateField"><span class="label-due">Due</span> Date: </label>
					<input type="text" name="date" id="dateField">
					
					<div class="hidden error"></div>
				</div>

				<div class="field half">
					<label for="timeField"><span class="label-due">Due</span> Time: </label>
					<select name="time" id="timeField">
				
						<?php 

						for ($i=1; $i < 24; $i++) {
							// 
							if($i < 7) {
								continue;
							} elseif($i < 12) {
								$period = "AM"; 
								$time = $i;
							} else {
								$period = "PM";
								$time = ($i - 12) ? $i - 12 : 12;
							}
							$hour = $time . ":00 ". $period;
							$sqlhour = $i . ":00:00";
							$hourAndHalf = $time . ":30 ". $period;
							$sqlhourAndHalf = $i . ":30:00";

							echo "<option value='$sqlhour'>$hour</option>";
							echo "<option value='$sqlhourAndHalf'>$hourAndHalf</option>";
						}

					 	?>
					</select>
				</div>
			</div>

			<div class="row">
				<input type="submit" name="addAgenda" value="Add">
			</div>
			<input type="hidden" id= "user_id" name="user_id" value="<?php echo page::$USER_ID ?>">
		</form>

		<?php 

			// Selecting agendas for this teacher.
			$stmt = $dbc->prepare("
				SELECT ag.title, ac.category, ag.due_date, ag.due_time, ag.rate, `classes`.class_name 

				FROM `agendas` AS ag 
					INNER JOIN agenda_category AS ac ON ac.category_id = ag.category_id 
					INNER JOIN `classes` ON `classes`.id = ag.class_id 

				WHERE ag.user_id = :id ORDER BY ag.due_date
			");

			try {
				$stmt->bindParam(":id", page::$USER_ID);

				$stmt->execute();

				$agendas = $stmt->fetchAll();

			} catch(exception $e) {
				die("Sorry. Something went wron while retrieving your agenda :/");
			}

			// If there was any row/agenda returned, do this.
			if($agendas) {

				foreach ($agendas as $agenda) {	?>

					<article class="agenda">
						<div class="head">
							<h2 class="title"><?php echo $agenda->title; ?></h2>

							<p class="date">Due: <?php echo Format::due_date($agenda->due_date); ?></p>

							<p style="clear:both;"></p>
						</div>
						<div class="more">
							<p class="due_date">Due: <?php echo $agenda->due_date ?></p>
							<p class="time"><?php echo $agenda->due_time ?></p>
							<p class="class_name">Class: <?php echo $agenda->class_name ?></p>
							<p class="category">Category: <?php echo $agenda->category ?></p>
						</div>
					</article>
				<?php
				} 
			} else {
				echo "<p class='none'>You have no agendas :)</p>";
			}
		?>
	</main>
	
</body></html>