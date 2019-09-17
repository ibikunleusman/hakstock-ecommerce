<?php
	require_once '../core/init.php';
	if (!is_logged_in()) {
		login_error_redirect();
	}
	if (!has_permission('admin')) {
		permission_error_redirect('index.php');
	}
	include 'includes/head.php';
	include 'includes/navigation.php';
	// Get current date
	$currentdate=getdate(date("U"));

	$user_query = $db->query("SELECT * FROM users ORDER BY full_name");

	if (isset($_GET['delete'])) {
		$delete_id = int($_GET['id']);
		$delete_id = checkInput($delete_id);
		$db->query("DELETE FROM users WHERE id = '$delete_id'");
		$_SESSION['success_flash'] = 'User has been deleted.';
		header('Location: users.php');
	}

	if (isset($_GET['add'])) {
		$full_name = ((isset($_POST['full_name']))?checkInput($_POST['full_name']):'');
		$email = ((isset($_POST['email']))?checkInput($_POST['email']):'');
		$password = ((isset($_POST['password']))?checkInput($_POST['password']):'');
		$confirm_pass = ((isset($_POST['confirm_pass']))?checkInput($_POST['confirm_pass']):'');
		$permissions = ((isset($_POST['permissions']))?checkInput($_POST['permissions']):'');
		$error = array();

		if ($_POST) {
			$email_sql = $db->query("SELECT * FROM users WHERE email = '$email'");
			$email_num = mysqli_num_rows($email_sql);

			if ($email_num != 0) {
				$error[] = 'Email already exists.';
			}

			$required = array('full_name','email','password','confirm_pass','permissions');
			foreach ($required as $field) {
				if ($_POST[$field] == '') {
					$error[] = 'Please fill out required fields.';
					break;
				}
			}

			if (strlen($password) < 8) {
				$error[] = 'Password cannot be less than 8 characters.';
			}

			if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
				$error[] = "Invalid email.";
			}
			// If new password matches confirmed password.
			if ($password != $confirm_pass) {
				$error[] = 'New password does not match confirm password.';
			}

			if (!empty($error)) {
				echo show_error($error);
			}
			else {
				// Add user
				$hash_pass = password_hash($password,PASSWORD_DEFAULT);
				$adduser_sql = $db->query("INSERT INTO users (full_name, email, password, permissions) VALUES ('$full_name','$email','$hash_pass','$permissions')");
				$_SESSION['success_flash'] = 'You have successfully added a user.';
				header('Location: users.php');
			}
		}

	?>
	<hr>
	<!-- Display current date -->
	<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
	<h4 style="margin-left: 30px; font-weight: bold">Add User</h4>
	<hr>
	<div class="container-fluid">
		<form action="users.php?add=1" method="post">
			<div class="form-group col-md-4">
				<label for="full_name">Name:</label>
				<input type="full_name" name="full_name" id="full_name" class="form-control" value="<?php echo $full_name; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="email">E-mail:</label>
				<input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="new_password">Password:</label>
				<input type="password" name="password" id="password" class="form-control" value="<?php echo $password; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="password">Confirm Password:</label>
				<input type="password" name="confirm_pass" id="confirm_pass" class="form-control" value="<?php echo $confirm_pass; ?>">
			</div>
			<div class="form-group col-md-4">
				<label for="permissions">Permissions:</label>
				<select class="form-control" name="permissions">
					<option value=""<?php echo (($permissions == '')?' selected':''); ?>></option>
					<option value="editor"<?php echo (($permissions == 'editor')?' selected':''); ?>>Editor</option>
					<option value="admin,editor"<?php echo (($permissions == 'admin,editor')?' selected':''); ?>>Admin</option>
				</select>
			</div>
			<div class="form-group col-md-4">
				<br>
				<a href="index.php" class="ghost-button pull-right">Cancel</a>
				&nbsp;
				<input type="submit" class="ghost-button pull-right" value="Save">
			</div>
		</form>
	</div>
		<?php
	}
	else {

?>
<hr>
<!-- Display current date -->
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
<h3 style="margin-left: 30px; font-weight: bold">Users</h3>

<a href="users.php?add=1" class="ghost-button pull-right" id="add-product-btn">Add User</a>
<div class="clearfix"></div>
<hr>

<table class="table table-bordered table-striped table-condensed table-auto">
	<thead>
		<th>Name</th>
		<th>Email</th>
		<th>Date Joined</th>
		<th>Last Login</th>
		<th>Permissions</th>
		<th></th>
	</thead>
	<tbody>
		<?php while($user = mysqli_fetch_assoc($user_query)): ?>
			<tr>
				<td><?php echo $user['full_name']; ?></td>
				<td><?php echo $user['email']; ?></td>
				<td><?php echo $user['date_joined']; ?></td>
				<td><?php echo (($user['last_login'] == '0000-00-00 00:00:00')?'User has never logged in':$user['last_login']); ?></td>
				<td><?php echo $user['permissions']; ?></td>
				<td>
					<a href="users.php?delete=<?php echo $user['id']; ?>">Delete</a>
				</td>
			</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php  
	} include 'includes/footer.php';
?>