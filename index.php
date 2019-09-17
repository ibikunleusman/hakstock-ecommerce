<!-- Customer Home page. 
The 'include' and 'require once' function take all the text in specified files and copy them into the index.php file. -->
<?php
    require_once 'core/init.php'; // Require init.php file.
    include 'includes/head.php'; // Includes head.php file.
    include 'includes/navigation.php'; // Includes navigation.php file.
    include 'includes/banner.php'; // Includes banner.php file.
    include 'includes/feature.php'; // Includes feature.php file.
    include 'includes/sidebar.php'; // Includes sidebar.php file.

    $newsql = "SELECT * FROM products WHERE new = 1"; // SQL query to retrive new products.
    $newproduct = $db->query($newsql);
?>
    
<!-- Display new products on arrival -->
    		<div class="col-md-8">
    			<div class="row">
    				<h2 class="text-center">New Arrivals</h2>
                    <!-- Loop through products assigned with new product id and display in new arrivals section. -->
                    <?php while($product = mysqli_fetch_assoc($newproduct)) : ?>
        				<div class="col-md-3 text-center">
        					<h4><?php echo $product['title']; ?></h4>
                            <?php $images = explode(',',$product['image']); ?>
        					<img src="<?php echo $images[0]; ?>" alt="<?php echo $product['title']; ?>" class="product-image">
        					<p class="price">List Price: <s>$<?php echo $product['list_price']; ?></s></p>
        					<p class="list-price text-danger">Sale Price: $<?php echo $product['sale_price']; ?></p>
        					<p><a class="ghost-button" onclick="alert('Product details is under construction. Modal dialog error')">Quick Look</a></p>
        				</div>
                    <?php endwhile; ?>    				
    			</div>
    		</div>

<?php    
    include 'includes/rightsidebar.php'; // Includes rightsidebar.php file.
    include 'includes/footer.php';       // Includes footer.php file.
?>