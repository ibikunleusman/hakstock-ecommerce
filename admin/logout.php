<?php
	// The 'include' and 'require once' function take all the text in specified files and copy them into the categories.php file
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	unset($_SESSION['SBUser']);
	header('Location: login.php');

?>