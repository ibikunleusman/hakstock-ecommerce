<?php
    require_once 'core/init.php'; // Require init.php file.
    include 'includes/head.php'; // Includes head.php file.
    include 'includes/navigation.php'; // Includes navigation.php file.
    include 'includes/sidebar.php'; // Includes sidebar.php file.

    if (isset($_GET['cat'])) {
        $cat_id = checkInput($_GET['cat']);
    }
    else {
        $cat_id = '';
    }

    $sql = "SELECT * FROM products WHERE categories = '$cat_id'"; // SQL query to retrive new products.
    $productQ = $db->query($sql);
    $category = get_category($cat_id);
?>
    
<!-- Display new products on arrival -->
    		<div class="col-md-8">
    			<div class="row">
    				<h3 class="text-center"><?php echo 'Hakstock/' .$category['parent']. '/' . $category['child']; ?></h3>
                    <!-- Loop through products assigned with new product id and display in new arrivals section. -->
                    <?php while($product = mysqli_fetch_assoc($productQ)) : ?>
        				<div class="col-md-3 text-center">
        					<h4><?php echo $product['title']; ?></h4>
                            <?php $images = explode(',',$product['image']); ?>
        					<img src="<?php echo $images[0]; ?>" alt="<?php echo $product['title']; ?>" class="product-image">
        					<p class="price">List Price: <s>$<?php echo $product['list_price']; ?></s></p>
        					<p class="list-price text-danger">Sale Price: $<?php echo $product['sale_price']; ?></p>
        					<p><a class="ghost-button" onclick="detailsmodal(<?= $product['id']; ?>)">Quick Look</a></p>
        				</div>
                    <?php endwhile; ?>    				
    			</div>
    		</div>

<?php    
    include 'includes/rightsidebar.php'; // Includes rightsidebar.php file.
    include 'includes/footer.php';       // Includes footer.php file.
?>