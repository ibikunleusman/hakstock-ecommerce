<?php
    require_once 'core/init.php'; // Require init.php file.
    include 'includes/head.php'; // Includes head.php file.
    include 'includes/navigation.php'; // Includes navigation.php file.
    include 'includes/sidebar.php'; // Includes sidebar.php file.

    $sql = "SELECT * FROM products";
    $cat_id = (($_POST['cat'] != '')?checkInput($_POST['cat']):'');
    if ($cat_id == '') {
        $sql .= ' WHERE removed = 0';
    }
    else {
        $sql .= " WHERE categories = '{$cat_id}' AND removed = 0";
    }
    $price_sort = (($_POST['price_sort'] != '')?checkInput($_POST['price_sort']):'');
    $min_price = (($_POST['min_price'] != '')?checkInput($_POST['min_price']):'');
    $max_price = (($_POST['max_price'] != '')?checkInput($_POST['max_price']):'');
    $brand = (($_POST['brand'] != '')?checkInput($_POST['brand']):'');
    if ($min_price != '') {
        $sql .= " AND sale_price >= '{$min_price}'";
    }
    if ($max_price != '') {
        $sql .= " AND sale_price <= '{$max_price}'";
    }
    if ($brand != '') {
        $sql .= " AND brand = '{$brand}'";
    }
    if ($price_sort == 'low') {
        $sql .= " ORDER BY sale_price";
    }
    if ($price_sort == 'high') {
        $sql .= " ORDER BY sale_price DESC";
    }

    $productQ = $db->query($sql);
    $category = get_category($cat_id);
?>
    
<!-- Display new products on arrival -->
    		<div class="col-md-8">
    			<div class="row">
                    <?php if($cat_id != ''): ?>
    				    <h3 class="text-center"><?php echo 'Hakstock/' .$category['parent']. '/' . $category['child']; ?></h3>
                    <?php else: ?>
                        <h3 class="text-center">Hakstock</h3>
                    <?php endif; ?>
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