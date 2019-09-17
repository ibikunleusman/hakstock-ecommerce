<!-- This is the Administrator Login Page -->
<?php
	// The 'include' and 'require once' function take all the text in specified files and copy them into the categories.php file
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	if (!is_logged_in()) {
		login_error_redirect();
	}
	include 'includes/head.php'; // Include head.php file.
	include 'includes/navigation.php';

	$currentdate=getdate(date("U"));

	$full_name = ((isset($_POST['full_name']))?checkInput($_POST['full_name']):'');
	$full_name = trim($full_name);
	$email = ((isset($_POST['email']))?checkInput($_POST['email']):'');
	$email = trim($email);
	$hashed = $userdata['password'];
	$old_password = ((isset($_POST['old_password']))?checkInput($_POST['old_password']):'');
	$old_password = trim($old_password);
	$new_password = ((isset($_POST['new_password']))?checkInput($_POST['new_password']):'');
	$new_password = trim($new_password);
	$confirm_pass = ((isset($_POST['confirm_pass']))?checkInput($_POST['confirm_pass']):'');
	$confirm_pass = trim($confirm_pass);
	$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
	$userid = $userdata['id'];

	$error = array();
?>

<hr>
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
<h3 class="text-center">Edit Profile</h3>

<!-- Admin Login form -->
<div id="edit-form">
	<div>
		<?php
			if ($_POST) {
				// Form validation. 
				// If email or password is blank.
				if (empty($_POST['old_password']) || empty($_POST['new_password']) || empty('confirm_pass')) {
					$error[] = 'Fill out all fields.';
				}

				// Validate email input.
				if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
					$error[] = "Invalid email.";
				}

				// Password length validation. Must not be less than 6 characters.
				if (strlen($new_password) < 8) {
					$error[] = 'Password must not be less than 8 characters.';
				}

				$userquery = $db->query("SELECT * FROM users WHERE email = '$email'");
				$user = mysqli_fetch_assoc($userquery);

				// If new password matches confirmed password.
				if ($new_password != $confirm_pass) {
					$error[] = 'New password does not match confirm password.';
				}

				if (!password_verify($old_password, $hashed)) {
					$error[] = 'Password does not match our records.';
				}

				// Check for errors.
				if(!empty($error))
				{
					echo show_error($error);
				}
				else {
					// Update profile.
					$db->query("UPDATE users SET full_name = '$full_name', email = '$email', password = '$new_hashed' WHERE id = '$userid'");
					$_SESSION['success_flash'] = 'Profile has been updated.';
					header('Location: index.php');
				}
			}

		?>
	</div>
	
	<div class="container-fluid">
		<form action="edit_admin.php" method="post">
			<div class="form-group col-md-4">
				<label for="full_name">Name:</label>
				<input type="full_name" name="full_name" id="full_name" class="form-control" value="<?php echo $full_name; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="email">E-mail:</label>
				<input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="old_password">Old Password:</label>
				<input type="password" name="old_password" id="old_password" class="form-control" value="<?php echo $old_password; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="new_password">New Password:</label>
				<input type="password" name="new_password" id="new_password" class="form-control" value="<?php echo $new_password; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="password">Confirm Password:</label>
				<input type="password" name="confirm_pass" id="confirm_pass" class="form-control" value="<?php echo $confirm_pass; ?>">
			</div>
			<div class="form-group col-md-4">
				<br>
				<a href="index.php" class="ghost-button pull-right">Cancel</a>
				&nbsp;
				<input type="submit" class="ghost-button pull-right" value="Update">
			</div>
		</form>
	</div>
	
	
</div>


<?php include 'includes/footer.php'; // Include footer.php file. ?>