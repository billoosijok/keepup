<?php
require_once 'db_connect.php';

$table = $_POST['table'];
$rowId = $_POST['id'];

$sql = "DELETE FROM $table WHERE id = '$rowId'";

$dbc->query($sql);