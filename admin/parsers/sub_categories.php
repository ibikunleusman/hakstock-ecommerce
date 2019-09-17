<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	$parentID = (int)$_POST['parentID'];
	$selected = checkInput($_POST['selected']);
	$subcat_query = $db->query("SELECT * FROM categories WHERE parent = '$parentID' ORDER BY category");
	ob_start(); // Start buffering.
?>
	<option value=""></option>
	<?php while($subcat = mysqli_fetch_assoc($subcat_query)): ?>
		<option value="<?php echo $subcat['id']; ?>"<?php echo (($selected == $subcat['id'])?' selected':''); ?>>
			<?php echo $subcat['category']; ?>
		</option>
	<?php endwhile; ?>

<?php echo ob_get_clean(); ?>