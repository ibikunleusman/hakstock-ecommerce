<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	if (!is_logged_in()) {
		login_error_redirect();
	}
	include 'includes/head.php'; // Include head.php file.
	include 'includes/navigation.php'; // Include navigation.php file.
	$currentdate=getdate(date("U"));   // Get current date

	$sql = "SELECT * FROM products WHERE removed = 1";
	$result = $db->query($sql);

	if (isset($_GET['restore'])) {
		$restore_id = checkInput($_GET['restore']);
		$db->query("UPDATE products SET removed = 0 WHERE id = '$restore_id'");
		header('Location: deletedproducts.php');
	}

?>
<hr>
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
<h3 style="margin-left: 30px; font-weight: bold">Deleted Products</h3>
<hr>

<table class="table table-bordered table-striped table-condensed table-auto">
	<thead>
		<th>Product</th>
		<th>Category</th>
		<th>Sale Price</th>
		<th>Quantity Sold</th>
		<th></th>
	</thead>
	<tbody>
		<?php while($product = mysqli_fetch_assoc($result)): 
			$subcat_id = $product['categories'];
			$cat_sql = "SELECT * FROM categories WHERE id = '$subcat_id'";
			$cat_result = $db->query($cat_sql);
			$cat = mysqli_fetch_assoc($cat_result);
			$parent_id = $cat['parent'];
			$parent_sql = "SELECT * FROM categories WHERE id = '$parent_id'";
			$parent_result = $db->query($parent_sql);
			$parent = mysqli_fetch_assoc($parent_result);
			$category = $parent['category'].'>>'.$cat['category'];
		?>
			<tr>
				<td><?php echo $product['title']; ?></td>
				<td><?php echo $category; ?></td>
				<td><?php echo currency($product['sale_price']); ?></td>
				<td>0</td>
				<td>
					<a href="deletedproducts.php?restore=<?php echo $product['id']; ?>">Restore</a>
				</td>
			</tr>
		<?php endwhile; ?>
	</tbody>
</table>


<?php include 'includes/footer.php'; ?>