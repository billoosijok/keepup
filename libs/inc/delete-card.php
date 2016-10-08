<?php 
require_once "db_connect.php";

if(isset($_POST)) {
	$user_id = $_POST['user_id'];
	$agenda_id = $_POST['card_id'];

	if(isset($_POST['delete'])) {
		$stmt = $dbc->prepare("INSERT INTO user_agenda (user_id, agenda_id, status) VALUES (?,?,0)");
	} elseif (isset($_POST['undelete'])) {
		$stmt = $dbc->prepare("DELETE FROM user_agenda WHERE user_id=? AND agenda_id=?");
	}
	

	$stmt->execute([$user_id, $agenda_id]);

} else {
	echo "NOPE";
}