<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php';
	$name = checkInput($_POST['full_name']);
	$email = checkInput($_POST['email']);
	$street1 = checkInput($_POST['street1']);
	$street2 = checkInput($_POST['street2']);
	$city = checkInput($_POST['city']);
	$state = checkInput($_POST['state']);
	$zip = checkInput($_POST['zip']);
	$country = checkInput($_POST['country']);
	$success = 'passed';
	$error = array();
	$required = array(
		'full_name' => 'Full Name',
		'email' 	=> 'Email',
		'street1' 	=> 'Street Address',
		'city' 		=> 'City',
		'state' 	=> 'State',
		'zip' 		=> 'Zip Code',
		'country' 	=> 'Country',
	);

	// Check if all required fields are filled.
	foreach ($required as $f => $d) {
		if (empty($_POST[$f]) || $_POST[$f] = '') {
			$error[] = $d. ' is required.';
		}
	}

	if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
		$error[] = 'Invalid email.';
	}

	if (!empty($error)) {
		echo show_error($error);
	}
	else {
		echo $success;
	}	
?>