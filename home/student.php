 <?php 
$libs = "../libs";
require "$libs/inc/db_connect.php";
require "$libs/inc/classes.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
		page::head('Welcome');
		if (page::$USER_ROLE != "S") {
			header("Location: index.php");
		}
	?>
</head>
<body>
	<?php page::nav(); ?>
	<main id="content">
	<?php 
	$stmt = $dbc->prepare("SELECT `agendas`.title, `agendas`.due_time, `agendas`.due_date, `agendas`.class_id, `classes`.class_id, `classes`.class_name, `classes`.class_code, `user_class`.user_id, `user_class`.class_id, `agenda_category`.category FROM agendas INNER JOIN agenda_category ON `agendas`.category_id = `agenda_category`.category_id INNER JOIN classes ON `agendas`.class_id = `classes`.class_id INNER JOIN user_class ON `user_class`.user_id = ? ORDER BY due_date");

	try {
		$stmt->bindParam(1, page::$USER_ID);

	$stmt->execute();

	$resultSet = $stmt->fetchAll();
	} catch (exception $e) {
		echo "<p>" . $e->getMessage() . "</p>";
		die();
	}
	?>
		
	<?php 
	foreach ($resultSet as $agenda) {
		?>
		<article class="card <?php echo $agenda->category ?>">
			<h2><?php echo strtoupper($agenda->class_code) . " - " . 
							$agenda->class_name?> </h2>
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
		<?php
	}
	?>
			
		

	</main>
</body>
</html>

