<?php

/* This is the root index that handles the initial redirection */

session_start();
# If it is set then the user logged in
# otherwise they go to the 'login' portal
if(isset($_SESSION['user_info'])) { 
	header("Location: home");
	} else { 
	header("Location: login");

}