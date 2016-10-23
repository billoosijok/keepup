<?php 
/* This script adds an agenda to the database */
	require_once 'db_connect.php';
	require_once 'classes.php';

	if(isset($_POST['addAgenda'])) {
		$category_id 	= $_POST['category'];
		$class_id 	= $_POST['class'];
		$title 	= $_POST['title'];
		$rate 	= $_POST['rate'];
		$date 	= $_POST['date'];
		$time 	= $_POST['time'];
		$user_id 	= $_POST['user_id'];

		$sql = "INSERT INTO `agendas` (`title`, `rate`,`category_id`,`due_date`,`due_time`,`class_id`,`user_id`) 
			VALUES (?,?,?,?,?,?,?)";

		try {
			$stmt = $dbc->prepare($sql);

			$stmt->bindParam(1, $title);
			$stmt->bindParam(2, $rate);
			$stmt->bindParam(3, $category_id);
			$stmt->bindParam(4, Format::sqlDate($date));

			$stmt->bindParam(5, $time);
			$stmt->bindParam(6, $class_id);
			$stmt->bindParam(7, $user_id);

			$stmt->execute();

		} catch (exception $e) {
			echo $e->getMessage();
		}

	}
?>