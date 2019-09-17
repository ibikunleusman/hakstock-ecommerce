<!-- Manage Categories Page. 
The 'include' and 'require once' function take all the text in specified files and copy them into the categories.php file. -->
<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	if (!is_logged_in()) {
		login_error_redirect();
	}
	include 'includes/head.php'; // Include head.php file.
	include 'includes/navigation.php'; // Include navigation.php file.

	// Get current date
	$currentdate=getdate(date("U"));
	// Fetch main categories from database.
	$parentsql = "SELECT * FROM categories WHERE parent = 0";
	$result = $db->query($parentsql);

	$category = '';
	$post_parent = '';
	// Error array
	$error = array();

	// Delete Category
	if (isset($_GET['delete']) && !empty($_GET['delete'])) {
		$delete_id = (int)$_GET['delete'];
		$delete_id = checkInput($delete_id);
		$deletecategorysql = "DELETE FROM categories WHERE id = '$delete_id'";
		$deletecategoryresult = $db->query($deletecategorysql);
		$category = mysqli_fetch_assoc($deletecategoryresult);
		if ($category['parent'] == 0) {
			$deleteparentsql = "DELETE FROM categories WHERE parent = '$delete_id'";
			$deleteparentresult = $db->query($deleteparentsql);
		}

		$deletesql = "DELETE FROM categories WHERE id = '$delete_id'";
		$deleteresult = $db->query($deletesql);
		header('Location: categories.php');
	}

	// Edit Category
	if (isset($_GET['edit']) && !empty($_GET['edit'])) {
		$edit_id = (int)$_GET['edit'];
		$edit_id = checkInput($edit_id);
		$editcategorysql = "SELECT * FROM categories WHERE id = '$edit_id'";
		$editcategoryresult = $db->query($editcategorysql);
		$editcategory = mysqli_fetch_assoc($editcategoryresult);
	}

	// Form processing
	if (isset($_POST) && !empty($_POST)) {
		$post_parent = checkInput($_POST['parent']);
		$category = checkInput($_POST['category']);
		$catsql = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
		if (isset($_GET['edit'])) {
			$cat_id = $editcategory['id'];
			$catsql = "SELECT * categories WHERE category = '$category' AND parent = '$post_parent' AND id != '$cat_id'";
		}
		$catresult = $db->query($catsql);
		$rows = mysqli_num_rows($catresult);
		// If category is blank
		if ($category == '') {
			$error[] .= 'Please enter a category.';
		}

		// If category already exists.
		if ($rows > 0) {
			$error[] .= 'Category already exist.';
		}

		if (!empty($error)) {
			// Display error
			$display = show_error($error); ?>
			<script>
				jQuery('document').ready(function(){
					jQuery('#errors').html('<?php echo $display; ?>');
				});
			</script>
		<?php 
		} else {
			// Add category
			$update = "INSERT INTO categories (category, parent) VALUES ('$category','$post_parent')";
			if (isset($_GET['edit'])) {
				$update = "UPDATE categories SET category = '$category', parent = '$post_parent' WHERE id = '$edit_id'";
			}
			$db->query($update);
			header('Location: categories.php');
		}
		
	}

	$category_value = '';
	$parent_value = 0;
	if (isset($_GET['edit'])) {
		$category_value = $editcategory['category'];
		$parent_value = $editcategory['parent'];
	}
	else {
		if (isset($_POST)) {
			$category_value = $category;
			$parent_value = $post_parent;
		}
	}
?>
<hr>
<!-- Display current date -->
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
<h3 style="margin-left: 30px; font-weight: bold">Categories</h3>
<hr>
<div class="row">
	<!-- Categories Table -->
	<div class="col-md-6">
		<table class="table table-bordered table-auto">
			<thead>
				<th>Category</th>
				<th></th>
			</thead>
			<tbody>
				<?php while($parent = mysqli_fetch_assoc($result)) : 
					$parent_id = (int)$parent['id'];
					// Get sub-categories from database.
					$childsql = "SELECT * FROM categories WHERE parent = '$parent_id'";
					$childresult = $db->query($childsql);
				?>				
					<tr class="grey-bg">
						<td><?php echo $parent['category']; ?></td>
						<td>
							<a href="categories.php?edit=<?php echo $parent['id']; ?>">Edit</a>
							&nbsp;
							<a href="categories.php?delete=<?php echo $parent['id']; ?>">Delete</a>
						</td>
					</tr>
					<?php while($child = mysqli_fetch_assoc($childresult)) : ?>
						<tr>
							<td><?php echo $child['category']; ?></td>
							<td>
								<a href="categories.php?edit=<?php echo $child['id']; ?>">Edit</a>
								&nbsp;
								<a href="categories.php?delete=<?php echo $child['id']; ?>">Delete</a>
							</td>
						</tr>
					<?php endwhile; ?>
				<?php endwhile; ?>
			</tbody>
			
		</table>
	</div>

	<!-- Add Category Form -->
	<div class="col-md-6">
		<form class="form cat-form text-center" action="categories.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
			<legend><?php echo ((isset($_GET['edit']))?'Edit':'Add'); ?> Category</legend>
			<div id="errors"></div>
			<div class="form-group">
				<label for="parent">Main Categories</label>
				<!-- Categories options -->
				<select class="form-control" name="parent" id="parent">
					<option value="0"<?php echo (($parent_value == 0)?'selected="selected"':''); ?>>Category</option>
					<?php
					$parentsql = "SELECT * FROM categories WHERE parent = 0";
					$result = $db->query($parentsql);
					?>
					<!-- Fetch categories from database and add to select option -->
					<?php while($parent = mysqli_fetch_assoc($result)) : ?>
						<option value="<?php echo $parent['id']; ?>"<?php echo (($parent_value == $parent['id'])?' selected="selected"':''); ?>
						><?php echo $parent['category']; ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group">
				<label for="category">Enter Category</label>
				<input type="text" id="category" class="form-control" name="category" value="<?php echo $category_value; ?>">
			</div>
			<div class="form-group">
				<input type="submit" class="ghost-button" value="<?php echo ((isset($_GET['edit']))?'Edit':'Add'); ?> Category">
			</div>
		</form>
	</div>
</div>

<?php 
	include 'includes/footer.php'; // Include footer.php file 

?> 