<?php
	require_once 'core/init.php';
	include 'includes/head.php';
	include 'includes/navigation.php';
	
	if ($cartid != '') {
		$cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cartid}'");
		$result = mysqli_fetch_assoc($cartQ);
		$items = json_decode($result['items'],true);
		$i = 1;
		$subtotal = 0;
		$itemcount = 0;
	}

?>

<div class="container-fluid">
	<h3 class="text-center">Shopping Cart</h3>
	<?php if($cartid == ''): ?>
		<div class="container">
			<div class="display-cart">
				<p class="text-center text-danger">Your shopping cart is empty.</p>
			</div>
		</div>
	<?php else: ?>
		<div class="container-fluid">
			<div class="col-md-7 table-responsive">
				<table class="table table-condensed">
					<thead>
						<th>#</th>
						<th></th>
						<th>Item</th>
						<th>Price</th>
						<th>Size/Color</th>
						<th>Quantity</th>
						<th>Sub-Total</th>
					</thead>
					<tbody>
						<?php
							foreach ($items as $item) {
								$product_id = $item['id'];
								$productQ = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
								$product = mysqli_fetch_assoc($productQ);
								$sArray = explode(',', $product['size']);
								foreach ($sArray as $sizeString) {
									$s = explode(':',$sizeString);
									if ($s[0] == $item['size']) {
										$available = $s[1];
									}
								}
								?>

								<tr>
									<td><?php echo $i; ?></td>
									<td>
										<img src="<?php echo $product['image']; ?>" alt="Product Image" width="100" height="100">
									</td>
									<td><?php echo $product['title']; ?></td>
									<td><?php echo currency($product['sale_price']); ?></td>
									<td><?php echo $item['size']; ?></td>
									<td>
										<button class="btn btn-xs btn-default" onclick="update_cart('less','<?=$product['id'];?>','<?=$item['size'];?>');">-</button>
										<?php echo $item['quantity']; ?>
										<?php if($item['quantity'] < $available): ?>
											<button class="btn btn-xs btn-default" onclick="update_cart('add','<?=$product['id'];?>','<?=$item['size'];?>');">+</button>
										<?php else: ?>
											<span class="text-danger">Only <?=$available; ?> in stock</span>
										<?php endif; ?>
									</td>
									<td><?php echo currency($item['quantity'] * $product['sale_price']); ?></td>
								</tr>

								<?php
								$i++;
								$itemcount += $item['quantity'];
								$subtotal += ($product['sale_price'] * $item['quantity']);
							}

							$tax = TAXRATE * $subtotal;
							$tax = number_format($tax,2);
							$total = $tax + $subtotal;
						?>
					</tbody>
				</table>
			</div>
			<div class="col-md-5">
				<table class="table table-condensed">
					<thead>
						<th>Total Qty</th>
						<th>Subtotal</th>
						<th>Tax</th>
						<th>Total</th>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $itemcount; ?></td>
							<td><?php echo currency($subtotal); ?></td>
							<td><?php echo currency($tax); ?></td>
							<td style="font-size: 13pt;"><strong><?php echo currency($total); ?></strong></td>
						</tr>
					</tbody>
				</table>
				<div class="col-md-3">
					<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">

	  					<!-- Identify your business so that you can collect the payments. -->
			  			<input type="hidden" name="business" value="business@hakstocks.com">

			  			<!-- Specify a Buy Now button. -->
			  			<input type="hidden" name="cmd" value="_xclick">
			  			<input type="hidden" name="tx" value="TransactionID">
			  			<input type="hidden" name="amt" value="">

			  			<!-- Specify details about the item that buyers will purchase. -->

			  			<input type="hidden" name="item_name" value="<?php 
			  				foreach ($items as $item) {
								$product_id = $item['id'];
								$productQ = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
								$product = mysqli_fetch_assoc($productQ);
								$sArray = explode(',', $product['size']);
								foreach ($sArray as $sizeString) {
									$s = explode(':',$sizeString);
									if ($s[0] == $item['size']) {
										$available = $s[1];
									}
								}

							echo $product['title'];
							echo " & ";
							}
			  			?>">
			  			<input type="hidden" name="amount" value="<?php echo $total; ?>">
			  			<input type="hidden" name="currency_code" value="USD">

			  			<!-- Display the payment button. -->
			  			<input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="PayPal - The safer, easier way to pay online"><img alt="" border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif">
			  			<p class="text-center">--OR--</p>
			  			<a data-toggle="modal" data-target="#checkoutModal" class="ghost-button">Pay With Credit Card</a>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>	
</div>

<!-- Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="checkoutModalLabel">Shipping Address</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
	       	<form action="confirm.php" method="post" id="payment-form">
	       		<span class="bg-danger" id="payment-error"></span>
	       		<input type="hidden" name="tax" value="<?php echo $tax; ?>">
	       		<input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
	       		<input type="hidden" name="total" value="<?php echo $total; ?>">
	       		<input type="hidden" name="cartid" value="<?php echo $cartid; ?>">
	       		<input type="hidden" name="description" value="<?php echo $itemcount.' item'.(($itemcount>1)?'s':'').' from Hakstock.'; ?>">
	       		<div id="step1" style="display: block;">
	       			<div class="form-group col-md-6">
	       				<label for="full_name">Full Name:</label>
	       				<input type="text" class="form-control" id="full_name" name="full_name">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="email">Email:</label>
	       				<input type="email" class="form-control" id="email" name="email">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="street1">Street Address 1:</label>
	       				<input type="text" class="form-control" id="street1" name="street1" data-stripe="address_line1">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="street2">Street Address 2:</label>
	       				<input type="text" class="form-control" id="street2" name="street2" data-stripe="address_line2">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="city">City:</label>
	       				<input type="text" class="form-control" id="city" name="city" data-stripe="address_city">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="state">State:</label>
	       				<input type="text" class="form-control" id="state" name="state" data-stripe="address_state">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="zip">Zip:</label>
	       				<input type="text" class="form-control" id="zip" name="zip" data-stripe="address_zip">
	       			</div>
	       			<div class="form-group col-md-6">
	       				<label for="country">Country:</label>
	       				<input type="text" class="form-control" id="country" name="country" data-stripe="address_country">
	       			</div>
	       		</div>
	       		<div id="step2" style="display:none;">
	       			<div class="form-group col-md-3">
	       				<label for="name">Name on Card:</label>
	       				<input type="text" id="name" class="form-control" data-stripe="name">
	       			</div>
	       			<div class="form-group col-md-3">
	       				<label for="card_num">Card Number:</label>
	       				<input type="text" class="form-control" id="card_num" data-stripe="number">
	       			</div>
	       			<div class="form-group col-md-2">
	       				<label for="exp-month">Expire Month:</label>
	       				<select id="exp-month" class="form-control" data-stripe="exp_month">
	       					<option value=""></option>
	       					<?php for($i=1;$i < 13; $i++): ?>
	       						<option value="<?php echo $i; ?>">
	       							<?php echo $i; ?>
	       						</option>
	       					<?php endfor; ?>
	       				</select>
	       			</div>
	       			<div class="form-group col-md-2">
	       				<label for="exp-year">Expire Year:</label>
	       				<select id="exp-year" class="form-control" data-stripe="exp_year">
	       					<option value=""></option>
	       					<?php $y = date("Y"); ?>
	       					<?php for($i=0;$i < 11; $i++): ?>
	       						<option value="<?php echo $y+$i; ?>">
	       							<?php echo $y+$i; ?>
	       						</option>
	       					<?php endfor; ?>
	       				</select>
	       			</div>
	       			<div class="form-group col-md-2">
	       				<label for="cvc">CVC:</label>
	       				<input type="text" class="form-control" id="cvc" data-stripe="cvc">
	       			</div>
	       		</div>
       	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="ghost-button" data-dismiss="modal">Close</button>
        <button type="button" class="ghost-button" id="cont_btn" onclick="check_address();">Continue</button>
        <button type="button" class="ghost-button" id="back_btn" onclick="back_address();" style="display: none;">Go Back</button>
        <button type="submit" class="ghost-button" id="checkout_btn" style="display: none;">Check Out</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function back_address() {
	jQuery('#payment-error').html("");
	jQuery('#step1').css("display","block");
	jQuery('#step2').css("display","none");
	jQuery('#cont_btn').css("display","inline-block");
	jQuery('#back_btn').css("display","none");
	jQuery('#checkout_btn').css("display","none");
	jQuery('#checkoutModalLabel').html("Shipping Address");
}

	function check_address() {
		var data = {
			'full_name' : jQuery('#full_name').val(),
			'email' : jQuery('#email').val(),
			'street1' : jQuery('#street1').val(),
			'street2' : jQuery('#street2').val(),
			'city' 	: jQuery('#city').val(),
			'state' : jQuery('#state').val(),
			'zip'	: jQuery('#zip').val(),
			'country' : jQuery('#country').val(),
		};
		jQuery.ajax({
			url : '/hakstocks/admin/parsers/check_address.php',
			method : 'POST',
			data : data,
			success : function(data){
				if ($.trim(data) != 'passed') {
					jQuery('#payment-error').html(data);
				}
				if (data.trim() == 'passed') {
					jQuery('#payment-error').html("");
					jQuery('#step1').css("display","none");
					jQuery('#step2').css("display","block");
					jQuery('#cont_btn').css("display","none");
					jQuery('#back_btn').css("display","inline-block");
					jQuery('#checkout_btn').css("display","inline-block");
					jQuery('#checkoutModalLabel').html("Enter Your Card Details");
				}
			},
			error : function(){alert("Oops! Something went wrong.")},
		})
	}

	Stripe.setPublishableKey('<?php echo STRIPE_PUBLIC; ?>');

	function stripeResponseHandler(status, response) {
	  	var $form = $('#payment-form');

	  	if (response.error) {
	    	// Show the errors on the form
	    	$form.find('#payment-error').text(response.error.message);
	    	$form.find('button').prop('disabled', false);
	  	} else {
	    	// response contains id and card, which contains additional card details
	    	var token = response.id;
	    	// Insert the token into the form so it gets submitted to the server
	    	$form.append($('<input type="hidden" name="stripeToken" />').val(token));
	    	// and submit
	    	$form.get(0).submit();
	  	}
	};

	jQuery(function($) {
  		$('#payment-form').submit(function(event) {
    	var $form = $(this);

    	// Disable the submit button to prevent repeated clicks
    	$form.find('button').prop('disabled', true);

    	Stripe.card.createToken($form, stripeResponseHandler);

    	// Prevent the form from submitting with the default action
    	return false;
  		});
	});
</script>

<?php
	include 'includes/footer.php';
?>