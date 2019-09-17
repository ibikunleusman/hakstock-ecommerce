    <?php
    require_once '../core/init.php';
    $id = $_POST['id'];
    $id = (int)$id;
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = $db->query($sql);
    $product = mysqli_fetch_assoc($result);
    $sizestring = $product['size'];
    $sizestring = rtrim($sizestring,',');
    $size_array = explode(',', $sizestring);
    ?>

    <!-- Product Descriptions -->
    <?php ob_start(); ?>
    <div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true">
    	<div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center"><?= $product['title']; ?></h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6 fotorama">
                                <?php $images = explode(',',$product['image']);
                                foreach($images as $image): ?>
                                    <img src="<?= $image; ?>" class="details img-responsive" alt="<?= $product['title']; ?>">
                                <?php endforeach; ?>
                            </div>
                            <div class="col-sm-6">
                                <h5>Description</h5>
                                <p><?= nl2br($product['description']); ?></p>
                                <hr>
                                <p>Price: <s>$<?= $product['list_price']; ?></s> <span class="text-danger">$<?= $product['sale_price']; ?></span></p>
                                <form action="add_cart.php" method="post" id="add_form">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="available" id="available" value="">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <label for="quantity">Quantity:</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" min="0">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-7">
                                            <label for="size">Size/Color:</label>
                                            <select name="size" id="size" class="form-control">
                                                <option value=""></option>
                                                <?php foreach ($size_array as $string) {
                                                    $string_array = explode(':', $string);
                                                    $size = $string_array[0];
                                                    $available = $string_array[1];
                                                    if ($available > 0) {
                                                        echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' Available)</option>';
                                                    }                                           
                                                } ?>
                                                      
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <span id="modal_error" class="bg-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
    		<div class="modal-footer">
    			<a onclick="closeModal()" class="ghost-button">Close</a>
    			<a href="#" onclick="add_to_cart();return false" class="ghost-button"><span class="glyphicon glyphicon-shopping-cart"></span>Add to Cart</a>
    		</div>
    	</div>
    </div>

    <script>
    jQuery('#size').change(function() {
        var available = jQuery('#size option:selected').data("available");
        jQuery('#available').val(available);
    });

    $(function () {
        $('.fotorama').fotorama({
            'loop':true,
            'autoplay':true,
        });
    });

        function closeModal()
        {
            jQuery('#details-modal').modal('hide');
            setTimeout(function(){
                jQuery('#details-modal').remove();
            },500);
        }
    </script>
    <?php echo ob_get_clean(); ?>