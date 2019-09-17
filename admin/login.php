<!-- This is the Administrator Login Page -->
<?php
	// The 'include' and 'require once' function take all the text in specified files and copy them into the categories.php file
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	include 'includes/head.php'; // Include head.php file.
	$email = ((isset($_POST['email']))?checkInput($_POST['email']):'');
	$email = trim($email);
	$password = ((isset($_POST['password']))?checkInput($_POST['password']):'');
	$password = trim($password);
	$error = array();
?>

<style>
	body {
		background-image: url(/hakstocks/img/banner-2.jpg);
		background-size: cover;
		background-repeat: no-repeat;
		background-attachment: fixed;
	}

</style>

<!-- Admin Login form -->
<div id="login-form">
	<div>
		<?php
			if ($_POST) {
				// Form validation. 
				// If email or password is blank.
				if (empty($_POST['email']) || empty($_POST['password'])) {
					$error[] = 'Incorrect email or password.';
				}

				// Validate email input.
				if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
					$error[] = "Invalid email.";
				}

				// Password length validation. Must not be less than 6 characters.
				if (strlen($password) < 5) {
					$error[] = 'Password must not be less than 6 characters.';
				}

				// If user does not exists.
				$userquery = $db->query("SELECT * FROM users WHERE email = '$email'");
				$user = mysqli_fetch_assoc($userquery);
				$usernum = mysqli_num_rows($userquery);

				if ($usernum < 1) {
					$error[] = "The email and password you entered did not match our records. Please double-check and try again.";
				}

				if (!password_verify($password, $user['password'])) {
					$error[] = 'Incorrect password entered.';
				}

				// Check for errors.
				if(!empty($error))
				{
					echo show_error($error);
				}
				else {
					// Log user in.
					$user_id = $user['id'];
					login($user_id);
				}
			}

		?>
	</div>
	<h3 class="text-center">Admin Login</h3>
	<hr>
	<form action="login.php" method="post">
		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>">
		</div>
		<div class="form-group">
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" class="form-control" value="<?php echo $password; ?>">
		</div>
		<div class="form-group">
			<input type="submit" class="ghost-button" value="Log In">
		</div>
		<p class="text-right"><a href="/hakstocks/">Home Page</a></p>
	</form>
	
</div>


<?php include 'includes/footer.php'; // Include footer.php file. ?>