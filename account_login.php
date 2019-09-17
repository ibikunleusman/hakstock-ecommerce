<!-- This is the Customer Account Login Page -->
<?php
	// The 'include' and 'require once' function take all the text in specified files and copy them into the categories.php file
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	include 'includes/head.php'; // Include head.php file.
	$email = ((isset($_POST['email']))?checkInput($_POST['email']):'');
	$email = trim($email);
	$error = array();
?>

<!-- Login form -->
<div id="login-form">
	<div>
		<?php
			if ($_POST) {
				// Form validation. 
				// If email or password is blank.
				if (empty($_POST['email'])) {
					$error[] = 'Incorrect email';
				}

				// Validate email input.
				if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
					$error[] = "Invalid email.";
				}

				// If user does not exists.
				$userquery = $db->query("SELECT * FROM transactions WHERE email = '$email'");
				$user = mysqli_fetch_assoc($userquery);
				$usernum = mysqli_num_rows($userquery);

				if ($usernum < 1) {
					$error[] = "The email and password you entered did not match our records. Please double-check and try again.";
				}

				// Check for errors.
				if(!empty($error))
				{
					echo show_error($error);
				}
				else {
					// Log user in.
					$user_id = $user['id'];
					cust_login($user_id);
				}
			}

		?>
	</div>
	<h3 class="text-center">Account Login</h3>
	<hr>
	<form action="account_login.php" method="post">
		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>">
		</div>
		<div class="form-group">
			<input type="submit" class="ghost-button" value="Log In">
		</div>
		<p class="text-right"><a href="/hakstocks/">Home Page</a></p>
	</form>
	
</div>


<?php include 'includes/footer.php'; // Include footer.php file. ?>