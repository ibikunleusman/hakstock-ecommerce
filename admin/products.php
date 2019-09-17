<!-- Manage Products Page. 
The 'include' and 'require once' function take all the text in specified files and copy them into the categories.php file. -->
<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php'; // For connection to the database.
	if (!is_logged_in()) {
		login_error_redirect();
	}
	include 'includes/head.php'; // Include head.php file.
	include 'includes/navigation.php'; // Include navigation.php file.
	$currentdate=getdate(date("U"));   // Get current date

	// Delete Products.
	if (isset($_GET['delete'])) {
		$delete_id = checkInput($_GET['delete']);
		$db->query("UPDATE products SET removed = 1 WHERE id = '$delete_id'");
		$db->query("UPDATE products SET new = 0 WHERE id = '$delete_id'");
		header('Location: products.php');
	}

	$dbpath = '';
	// Add product form.
	if (isset($_GET['add']) || isset($_GET['edit'])) {
		$brand_query = $db->query("SELECT * FROM brand ORDER BY brand");
		$parent_query = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
		$title = ((isset($_POST['title']) && $_POST['title'] != '')?checkInput($_POST['title']):'');
		$brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?checkInput($_POST['brand']):'');
		$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?checkInput($_POST['parent']):'');
		$category = ((isset($_POST['child']) && !empty($_POST['child']))?checkInput($_POST['child']):'');
		$sale_price = ((isset($_POST['sale_price']) && $_POST['sale_price'] != '')?checkInput($_POST['sale_price']):'');
		$list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '')?checkInput($_POST['list_price']):'');
		$description = ((isset($_POST['description']) && $_POST['description'] != '')?checkInput($_POST['description']):'');
		$size = ((isset($_POST['size']) && $_POST['size'] != '')?checkInput($_POST['size']):'');
		$size = rtrim($size,',');
		$prod_image = '';

		if (isset($_GET['edit'])) {
			$edit_id = (int)$_GET['edit'];
			$productquery = $db->query("SELECT * FROM products WHERE id = '$edit_id'");
			$product = mysqli_fetch_assoc($productquery);
			if (isset($_GET['delete_image'])) {
				$imgcount = (int)$_GET['imgcount'] - 1;
				$images = explode(',',$product['image']);
				$image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgcount];
				unlink($image_url);
				unset($images[$imgcount]);
				$imageString = implode(',',$images);
				$db->query("UPDATE products SET image = '{$imageString}' WHERE id = '$edit_id'");
				header('Location: products.php?edit='.$edit_id);
			}
			$category = ((isset($_POST['child']) && $_POST['child'])?checkInput($_POST['child']):$product['categories']);

			$title = ((isset($_POST['title']) && $_POST['title'] != '')?checkInput($_POST['title']):$product['title']);
			$brand = ((isset($_POST['brand']) && $_POST['brand'] != '')?checkInput($_POST['brand']):$product['brand']);
			$pquery = $db->query("SELECT * FROM categories WHERE id = '$category'");
			$presult = mysqli_fetch_assoc($pquery);
			$parent = ((isset($_POST['parent']) && $_POST['parent'] != '')?checkInput($_POST['parent']):$presult['parent']);
			$sale_price = ((isset($_POST['sale_price']) && $_POST['sale_price'] != '')?checkInput($_POST['sale_price']):$product['sale_price']);
			$list_price = ((isset($_POST['list_price']))?checkInput($_POST['list_price']):$product['list_price']);
			$description = ((isset($_POST['description']) && $_POST['description'] != '')?checkInput($_POST['description']):$product['description']);
			$size = ((isset($_POST['size']) && $_POST['size'] != '')?checkInput($_POST['size']):$product['size']);
			$size = rtrim($size,',');
			$prod_image = (($product['image'] != '')?$product['image']:'');
			$dbpath = $prod_image;

		}
		if (!empty($size)) {
				$sizeString = checkInput($size);
				$sizeString = rtrim($sizeString,',');
				$sizesArray = explode(',', $sizeString);
				$sArray = array();
				$qArray = array();
				$tArray = array();
				foreach ($sizesArray as $ss) {
					$s = explode(':', $ss);
					$sArray[] = $s[0];
					$qArray[] = $s[1];
					$tArray[] = $s[2];
				}
			}
			else {
				$sizesArray = array();
			}

		if ($_POST) {
			$error = array();
			
			$required = array('title','brand','sale_price','parent','child','size');
			$allowed = array('png','jpg','jpeg','gif');
			$uploadpath = array();
			$tmpLoc = array();
			foreach ($required as $field) {
				if ($_POST[$field] == '') {
					$error[] = 'Please fill out required fields.';
					break;
				}
			}

			$photocount = count($_FILES['image']['name']);

			if ($photocount > 0) {
				for($i = 0;$i<$photocount;$i++) {
					$name = $_FILES['image']['name'][$i];
					$nameArray = explode('.', $name);
					$filename = $nameArray[0];
					$fileext = $nameArray[1];
					$mime = explode('/',$_FILES['image']['type'][$i]);
					$mimetype = $mime[0];
					$mimeext = $mime[1];
					$tmpLoc[] = $_FILES['image']['tmp_name'][$i];
					$filesize = $_FILES['image']['size'][$i];				
					$uploadname = md5(microtime().$i).'.'.$fileext;
					$uploadpath[] = BASEURL.'img/products/'.$uploadname;
					if($i != 0){
						$dbpath .= ',';
					}
					$dbpath .= '/hakstocks/img/products/'.$uploadname;
					if ($mimetype != 'image') {
						$error[] = 'Please choose an image file.';
					}
					if (!in_array($fileext, $allowed)) {
						$error[] = 'Only png, jpg, jpeg, or gif formats allowed.';
					}
					if ($filesize > 2000000) {
						$error[] = 'Image must not exceed 2MB.';
					}
					if ($fileext != $mimeext && ($mimeext == 'jpeg' && $fileext != 'jpg')) {
						$error[] = 'Incorrect file extension match.';
					}
				}
			}

			if (!empty($error)) {
				echo show_error($error);
			}
			else {
				if ($photocount > 0) {
					for ($i=0;$i<$photocount;$i++) { 
						move_uploaded_file($tmpLoc[$i],$uploadpath[$i]);
					}
				
				}
				
				$insertSql = "INSERT INTO products (title,brand,description,categories,image,list_price,sale_price,size) VALUES ('$title','$brand','$description','$category','$dbpath','$list_price','$sale_price','$size')";
				if (isset($_GET['edit'])) {
					$insertSql = "UPDATE products SET title = '$title', brand = '$brand', description = '$description', categories = '$category', image = '$dbpath', list_price = '$list_price', sale_price = '$sale_price', size = '$size' WHERE id = '$edit_id'";
				}
				$db->query($insertSql);

				header('Location: products.php');

			}
		}
		
	?>
		<hr>
		<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
		<h3 class="text-center"><?php echo ((isset($_GET['edit']))?'Edit':'Add New'); ?> Product</h2>
		<div class="container-fluid">
			<form action="products.php?<?php echo ((isset($_GET['edit']))?'edit='.$edit_id:'add=1'); ?>" method="post" enctype="multipart/form-data">
				<div class="form-group col-md-3">
					<label for="title">Title*: </label>
					<input type="text" class="form-control" name="title" id="title" value="<?php echo $title; ?>">
				</div>
				<div class="form-group col-md-3">
					<label for="brand">Brand*:</label>
					<select class="form-control" id="brand" name="brand">
						<option value=""<?php echo (($brand == '')?' selected':''); ?>></option>
						<?php while($b = mysqli_fetch_assoc($brand_query)): ?>
							<option value="<?php echo $b['id']; ?>"<?php echo (($brand == $b['id'])?' selected':''); ?>><?php echo $b['brand']; ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group col-md-3">
					<label for="parent">Main Category*:</label>
					<select class="form-control" id="parent" name="parent">
						<option value=""<?php echo (($parent == '')?' selected':''); ?>></option>
						<?php while($p = mysqli_fetch_assoc($parent_query)): ?>
							<option value="<?php echo $p['id']; ?>"<?php echo (($parent == $p['id'])?' selected':''); ?>><?php echo $p['category']; ?>
							</option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group col-md-3">
					<label for="child">Sub-Category*:</label>
					<select class="form-control" id="child" name="child">
						
					</select>
				</div>
				<div class="form-group col-md-3">
					<label for="sale_price">Sale Price*:</label>
					<input type="text" id="sale_price" name="sale_price" class="form-control" value="<?php echo $sale_price; ?>">
				</div>
				<div class="form-group col-md-3">
					<label for="list_price">List Price:</label>
					<input type="text" id="list_price" name="list_price" class="form-control" value="<?php echo $list_price; ?>">
				</div>
				<div class="form-group col-md-3">
					<label> </label>
					<button class="form-control btn btn-default" onclick="jQuery('#sizeModal').modal('toggle'); return false">Add Color/Size and Quantity</button>
				</div>
				<div class="form-group col-md-3">
					<label for="sizes">View Color/Size and Quantity</label>
					<input type="text" class="form-control" name="size" id="size" value="<?php echo $size; ?>" readonly>
				</div>
				<div class="form-group col-md-6">
					<?php if($prod_image != ''): ?>
						<?php 
							$imgcount = 1;
							$images = explode(',',$prod_image); ?>
							<?php foreach($images as $photo): ?>
						<div class="prod-image col-md-4">
							<img src="<?php echo $photo; ?>" alt="Product Image">
							<br>
							<a href="products.php?delete_image=1&edit=<?php echo $edit_id; ?>&imgcount=<?php echo $imgcount; ?>" class="text-danger">Delete Image</a>
						</div>
						<?php 
							$imgcount++;
							endforeach; ?>
					<?php else: ?>
						<label for="image">Product Image:</label>
						<input type="file" name="image[]" id="image" class="form-control" multiple>
					<?php endif; ?>
				</div>
				<div class="form-group col-md-6">
					<label for="description">Product Description:</label>
					<textarea id="description" class="form-control" name="description" rows="6">
						<?php echo $description; ?>
					</textarea>
				</div>
				<div class="form-group col-md-3">
					<a href="products.php" class="ghost-button">Cancel</a>
					<input type="submit" class="ghost-button pull-right" value="<?php echo ((isset($_GET['edit']))?'Save Changes':'Add Product'); ?>">
				</div>
				<div class="clearfix"></div>
			</form>

			<div class="modal fade" id="sizeModal" tabindex="-1" role="dialog" aria-labelledby="sizeModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-lg" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="sizeModalLabel">Size/Color and Quantity</h4>
			      </div>
			      <div class="modal-body">
			      	<div class="container-fluid">
			      		<?php for($i=1;$i <= 12;$i++): ?>
				      		<div class="form-group col-md-2">
				      			<label for="size<?php echo $i; ?>">Size/Color:</label>
				      			<input type="text" class="form-control" name="sizes<?php echo $i; ?>" id="sizes<?php echo $i; ?>" value="<?php echo ((!empty($sArray[$i-1]))?$sArray[$i-1]:''); ?>">
				      		</div>
				      		<div class="form-group col-md-2">
				      			<label for="qty<?php echo $i; ?>">Quantity:</label>
				      			<input type="number" name="qty<?php echo $i; ?>" id="qty<?php echo $i; ?>" value="<?php echo ((!empty($qArray[$i-1]))?$qArray[$i-1]:''); ?>" min="0" class="form-control">
				      		</div>
				      		<div class="form-group col-md-2">
				      			<label for="threshold<?php echo $i; ?>">Threshold:</label>
				      			<input type="number" name="threshold<?php echo $i; ?>" id="threshold<?php echo $i; ?>" value="<?php echo ((!empty($tArray[$i-1]))?$tArray[$i-1]:''); ?>" min="0" class="form-control">
				      		</div>
				      	<?php endfor; ?>
			      	</div>			      	
			      </div>
			      <div class="modal-footer">
			        <a class="ghost-button" data-dismiss="modal">Close</a>
			        <a class="ghost-button" onclick="updateSize(); jQuery('#sizeModal').modal('toggle');return false;">Save changes</a>
			      </div>
			    </div>
			  </div>
			</div>
		</div>
		

	<?php 
	}
	else {	
	$sql = "SELECT * FROM products WHERE removed = 0";
	$result = $db->query($sql);


	if (isset($_GET['new'])) {
		$new_id = (int)$_GET['id'];
		$new = (int)$_GET['new'];
		$newsql = "UPDATE products SET new = '$new' WHERE id = '$new_id'";
		$db->query($newsql);
		header('Location: products.php');
	}
?>

<hr>
<!-- Display current date -->
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>
<h3 style="margin-left: 30px; font-weight: bold">Manage Products</h3>
<a href="products.php?add=1" class="ghost-button pull-right" id="add-product-btn">Add Product</a>
<div class="clearfix"></div>
<hr>

<table class="table table-bordered table-striped table-auto table-condensed">
	<thead>
		<th>Product</th>
		<th>Category</th>
		<th>Price</th>
		<th>New(Featured)</th>
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
				<td>
					<a href="products.php?new=<?php echo (($product['new'] == 0)?'1':'0'); ?>&id=<?php echo $product['id']; ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-<?php echo (($product['new'] == 1)?'minus':'plus'); ?>"></span>
					</a>
					&nbsp; <?php echo (($product['new'] == 1)?'New Arrival':''); ?>
				</td>
				<td>
					<a href="products.php?edit=<?php echo $product['id']; ?>">Edit</a> &nbsp;
					<a href="products.php?delete=<?php echo $product['id']; ?>">Delete</a>
				</td>
			</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php 
}
?>

<?php include 'includes/footer.php'; // Include footer.php file. ?>

<script>
	jQuery('document').ready(function(){
		get_sub_categories('<?php echo $category; ?>');
	});
</script>