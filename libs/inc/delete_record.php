<?php
/* Deletes a record from a table that is sent thru $_POST as well. */
require_once 'db_connect.php';

$table = $_POST['table'];
$rowId = $_POST['id'];

$sql = "DELETE FROM $table WHERE id = '$rowId'";

$dbc->query($sql);