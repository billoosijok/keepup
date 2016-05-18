<?php
session_start();

if(isset($_SESSION['user_id'])) { 
	header("Location: home");

	} else { 
	header("Location: login");
	
}