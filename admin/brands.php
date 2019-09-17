<?php
	require_once '../core/init.php';
	if (!is_logged_in()) {
		login_error_redirect();
	}
	include 'includes/head.php';
	include 'includes/navigation.php';
	// Get current date.
	$currentdate=getdate(date("U"));
	// Fetch brands from database
	$sql = "SELECT * FROM brand ORDER BY brand";
	$result = $db->query($sql);
	// Catch error array.
	$error = array();

	// Delete Brand
	if (isset($_GET['delete']) && !empty($_GET['delete'])) {
		$delete_id = (int)$_GET['delete'];
		$delete_id = checkInput($delete_id);
		$sql = "DELETE FROM brand WHERE id = '$delete_id'";
		$result = $db->query($sql);
		header('Location: brands.php');
	}

	// Edit Brand
	if (isset($_GET['edit']) && !empty($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$edit_id = checkInput($edit_id);
		$editsql = "SELECT * FROM brand WHERE id = '$edit_id'";
		$editresult = $db->query($editsql);
		$editbrand = mysqli_fetch_assoc($editresult);
	}

	// Manage when 'add brand' form is submitted.
	if(isset($_POST['add_submit'])) {
		$brand = checkInput($_POST['brand']);
		// If input is blank.
		if ($_POST['brand'] == '') {
			$error[] .= 'Please enter a brand.';  // If form is blank, display error message.
		}
		// If brand already exists.
		$sql = "SELECT * FROM brand WHERE brand = '$brand'";
		if (isset($_GET['edit'])) {
			$sql = "SELECT * FROM brand WHERE brand = '$brand' AND id != '$edit_id'";
		}
		$result = $db->query($sql);
		$rows = mysqli_num_rows($result);
		if ($rows > 0) {
			$error[] .= 'Brand already exists.';
		}
		// Display error messages
		if (!empty($error)) {
			echo show_error($error);
		}
		else {
			// Add brand
			$sql = "INSERT INTO brand (brand) VALUES ('$brand')";
			if (isset($_GET['edit'])) {
				$sql = "UPDATE brand SET brand = '$brand' WHERE id = $edit_id";
			}
			$db->query($sql);
			header('Location: brands.php');
		}
	}
?>
<hr>

<!-- Display current date -->
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>

<!-- Header -->
<h3 style="margin-left: 30px; font-weight: bold">Brands</h3>
<hr>

<!-- Add Brand Form -->
<div class="text-center">
	<form class="form-inline" action="brands.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
		<div class="form-group">
			<?php 
			$brand_value = '';
			if (isset($_GET['edit'])) {
				$brand_value = $editbrand['brand'];
			}
			else {
				if (isset($_POST['brand'])) {
					$brand_value = checkInput($_POST['brand']);
				}
			} ?>
			<label for="brand"><?php echo ((isset($_GET['edit']))?'Edit':'Add'); ?> Brand:</label>
			<input type="text" name="brand" id="brand" class="form-control" value="<?php echo $brand_value; ?>">
			<?php if (isset($_GET['edit'])): ?>
				<a href="brands.php" class="ghost-button">Cancel</a>
			<?php endif; ?>
			<input type="submit" name="add_submit" value="<?php echo ((isset($_GET['edit']))?'Edit':'Add'); ?> Brand" class="ghost-button">
		</div>
	</form>	
</div>

<!-- Brands Table -->
<table class="table table-bordered table-striped table-auto table-condensed">
	<thead>
		<th>Brand Name</th><th></th><th></th>
	</thead>
	<tbody>
		<!-- Fetch brands in database and add to table. -->
		<?php while($brand = mysqli_fetch_assoc($result)): ?>
			<tr>
				<td><?= $brand['brand']; ?></td>
				<td><a href="brands.php?edit=<?=$brand['id']; ?>">Edit</a></td>			
				<td><a href="brands.php?delete=<?=$brand['id']; ?>">Delete</a></td>
			</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php  
	include 'includes/footer.php';
?>