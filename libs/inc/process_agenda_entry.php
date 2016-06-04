<?php 
	require 'db_connect.php';
	require 'classes.php';

	if(isset($_POST['addAgenda'])) {
		$category_id 	= $_POST['category'];
		$class_id 	= $_POST['class'];
		$title 	= $_POST['title'];
		$date 	= $_POST['date'];
		$time 	= $_POST['time'];
		$details 	= $_POST['details'];
		$user_id 	= $_POST['user_id'];

		$sql = "INSERT INTO `agendas` (`title`,`category_id`,`due_date`,`due_time`,`class_id`,`user_id`,`details`) 
			VALUES (?,?,?,?,?,?,?)";

		try {
			$stmt = $dbc->prepare($sql);

			$stmt->bindParam(1, $title);
			$stmt->bindParam(2, $category_id);
			$stmt->bindParam(3, Format::sqlDate($date));
			$stmt->bindParam(4, $time);
			$stmt->bindParam(5, $class_id);
			$stmt->bindParam(6, $user_id);
			$stmt->bindParam(7, $details);

			$stmt->execute();

		} catch (exception $e) {
			echo $e->getMessage();
		}

	}
?>