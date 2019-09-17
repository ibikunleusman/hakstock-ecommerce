<?php
// This function displays error messages.
function show_error($error){
	$message = '<ul class="bg-danger">';
	foreach ($error as $errormessage) {
		$message .= '<li class="text-danger">'.$errormessage.'</li>';
	}
	$message .= '</ul>';
	return $message;
}

// This function cleans HTML inputted into Add Brand form.
function checkInput($markup){
	return htmlentities($markup,ENT_QUOTES,"UTF-8");
}

// This function displays prices in the correct currency format.
function currency($amount) {
	return '$'.number_format($amount,2);
}

// Log-in function
function login($user_id) {
	$_SESSION['SBUser'] = $user_id;
	global $db;
	$date = date("Y-m-d H:i:s");
	$db->query("UPDATE users SET last_login = '$date' WHERE id = '$user_id'");
	$_SESSION['success_flash'] = 'You have successfully logged in.';
	header('Location: index.php');
}

// is logged in
function is_logged_in() {
	if (isset($_SESSION['SBUser']) && $_SESSION['SBUser'] > 0) {
		return true;
	}
	return false;
}

// if user is not logged in.
function login_error_redirect($url = 'login.php') {
	$_SESSION['error_flash'] = 'Please log in.';
	header('Location: '.$url);
}

function has_permission($permission='admin')
{
	global $userdata;
	$permissions = explode(',', $userdata['permissions']);
	if (in_array($permission,$permissions,true)) {
		return true;
	}
	return false;
}

function cust_login($cus_id) {
	$_SESSION['CUser'] = $cus_id;
	global $db;
	$_SESSION['success_flash'] = 'You have successfully logged in.';
	header('Location: account.php');
}

// is logged in
function cus_logged_in() {
	if (isset($_SESSION['CUser']) && $_SESSION['CUser'] > 0) {
		return true;
	}
	return false;
}

// if user is not logged in.
function cuslogin_error_redirect($url = 'account_login.php') {
	$_SESSION['error_flash'] = 'Please log in.';
	header('Location: '.$url);
}

function get_category($child_id)
{
	global $db;
	$id = checkInput($child_id);
	$sql = "SELECT p.id AS 'pid', p.category AS 'parent', c.id AS 'cid', c.category AS 'child' 
			FROM categories c 
			INNER JOIN categories p
			ON c.parent = p.id
			WHERE c.id = '$id'";
	$query = $db->query($sql);
	$category = mysqli_fetch_assoc($query);
	return $category;
}

function sizesToArray($string) {
	$sizesArray = explode(',',$string);
	$retrnArray = array();
	foreach ($sizesArray as $size) {
		$s = explode(':',$size);
		$returnArray[] = array(
			'size' 		=> $s[0], 
			'quantity' 	=> $s[1],
			'threshold' => $s[2],
		);
	}
	return $returnArray;
}

function sizesToString($sizes){
	$sizeString = '';
	foreach ($sizes as $size) {
		$sizeString .= $size['size'].':'.$size['quantity'].':'.$size['threshold'].',';
	}
	$trimmed = rtrim($sizeString, ',');
	return $trimmed;
}

?>
