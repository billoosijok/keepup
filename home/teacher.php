<?php 
// session_start();
$libs = "../libs";
require "$libs/inc/db_connect.php";
require "$libs/inc/classes.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php page::head('Welcome'); 
	
	if (page::$USER_ROLE != "T") {
		// die('error');
		header("Location: index.php");
		}
		?>
	<link rel="stylesheet" type="text/css" href="<?php echo $libs ?>/css/jquery-ui.css">
	<script src="<?php echo $libs ?>/js/jquery-ui.min.js"></script>
	<script src="<?php echo $libs ?>/js/validate_agenda_entry.js"></script>
</head>
<body>
	<?php page::nav(); ?>
	<main id="content">
		<div id="addToggle">+</div>
		<form id="addAgenda">
			<div class="row">
			<div class="field half">
			<label for="categoryField">Category: </label>
				<select name="category" id="categoryField">
				<?php 
					$stmt = $dbc->query("SELECT * FROM `agenda_category`");

					while($category = $stmt->fetch()) {
						echo "<option value='" . $category->category_id ."'>" . ucfirst($category->category) . "</option>";
					}
				?>
				</select>
				</div>
				<div class="field half">
			<label for="classField">Class: </label>
				<select name="class" id="classField">
					<?php 
						$stmt = $dbc->query("SELECT * FROM `classes`");

						while($class = $stmt->fetch()) {
							echo "<option value='" . $class->class_id ."'>" . ucfirst($class->class_name) . "</option>";
						}
					?>
				</select>
				</div>
				</div>
				<div class="row">
					<div class="field half">
						<label for="titleField">Title: </label><input type="text" name="title" id="titleField">
						<div class="hidden error"></div>
					</div>
				</div>
				<div class="row">
				<div class="field half">
					<label for="dateField"><span class="label-due">Due</span> Date: </label><input type="text" name="date" id="dateField">
					<div class="hidden error"></div>
				</div>
				<div class="field half">
			<label for="timeField"><span class="label-due">Due</span> Time: </label><input type="time" name="time" id="timeField">
			<div class="hidden error"></div>
			</div>
			</div>
			<div class="row">
			<div class="field">
			<label for="detailsField">Details: </label><textarea name="details" id="detailsField"></textarea>
			</div>
			</div>
			<div class="row">
				<input type="submit" name="addAgenda" value="Add">
			</div>
			<input type="hidden" id= "user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
		</form>
		<?php 
			$stmt = $dbc->prepare("SELECT ag.title, ac.category, ag.due_date, ag.due_time, ag.rate, ag.details, `classes`.class_name FROM `agendas` AS ag INNER JOIN agenda_category AS ac ON ac.category_id = ag.category_id INNER JOIN `classes` ON `classes`.class_id = ag.class_id WHERE ag.user_id = :id ORDER BY ag.due_date");

			try {
				$stmt->bindParam(":id",page::$USER_ID);

				$stmt->execute();

				$agendas = $stmt->fetchAll();

			} catch(exception $e) {
				echo $e->getMessage();
			}

			foreach ($agendas as $agenda) {
				
				?>
				<article class="agenda">
					<h2 class="title"><?php echo $agenda->title; ?></h2>
					<p class="date">Due: <?php echo Format::due_date($agenda->due_date) ?></p>
					<p style="clear:both;"></p>
					<div class="more">
						<p class="details"><?php echo $agenda->details ?></p>
						<p class="due_date">Due: <?php echo $agenda->due_date ?></p>
						<p class="time"><?php echo $agenda->due_time ?></p>
						<p class="class_name">Class: <?php echo $agenda->class_name ?></p>
						<p class="category">Category: <?php echo $agenda->category ?></p>
						<a href="#" id="dismiss">Less</a>
					</div>
				</article>
				<?php
			}
		?>
	</main>
</body>
</html>