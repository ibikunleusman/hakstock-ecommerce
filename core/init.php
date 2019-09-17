<?php
$db = mysqli_connect('db_host','db_username','db_password','db_hostname'); // Store connection into variable.

if (mysqli_connect_errno()) {									
	echo "Database Connection Failed: ". mysqli_connect_error(); // Display error message if database fails to connect.
	die(); // Kill connection.
}
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/config.php';
require_once BASEURL.'helpers/helper.php';
require BASEURL.'vendor/autoload.php';

$cartid = '';
if (isset($_COOKIE[CART_COOKIE])) {
	$cartid = checkInput($_COOKIE[CART_COOKIE]);
}

if (isset($_SESSION['SBUser'])) {
	$user_id = $_SESSION['SBUser'];
	$query = $db->query("SELECT * FROM users WHERE id = '$user_id'");
	$userdata = mysqli_fetch_assoc($query);
	$fname = explode(' ',$userdata['full_name']);
	$userdata['first'] = $fname[0];
	$userdata['last'] = $fname[1];
}

if (isset($_SESSION['CUser'])) {
	$cus_id = $_SESSION['CUser'];
	$cusquery = $db->query("SELECT * FROM transactions WHERE id = '$cus_id'");
	$cusdata = mysqli_fetch_assoc($cusquery);
	$cusname = explode(' ',$cusdata['full_name']);
	$cusdata['first'] = $fname[0];
	$cusdata['last'] = $fname[1];
}

if (isset($_SESSION['success_flash'])) {
	echo '<div class="bg-success"><p class="text-success text-center">'.$_SESSION['success_flash'].'</p></div>';
	unset($_SESSION['success_flash']);
}

if (isset($_SESSION['error_flash'])) {
	echo '<div class="bg-danger"><p class="text-danger text-center">'.$_SESSION['error_flash'].'</p></div>';
	unset($_SESSION['error_flash']);
}
