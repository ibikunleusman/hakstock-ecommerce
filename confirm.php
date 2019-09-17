<?php
require_once 'core/init.php';

// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(STRIPE_PRIVATE);

// Get the credit card details submitted by the form
$token = $_POST['stripeToken'];
$full_name = checkInput($_POST['full_name']);
$email = checkInput($_POST['email']);
$street1 = checkInput($_POST['street1']);
$street2 = checkInput($_POST['street2']);
$city = checkInput($_POST['city']);
$state = checkInput($_POST['state']);
$zip = checkInput($_POST['zip']);
$country = checkInput($_POST['country']);
$tax = checkInput($_POST['tax']);
$subtotal = checkInput($_POST['subtotal']);
$total = checkInput($_POST['total']);
$cartid = checkInput($_POST['cartid']);
$description = checkInput($_POST['description']);
$charge_amount = number_format($total,2) * 100;
$metadata = array(
	"cartid" 	=> $cartid,
	"tax" 		=> $tax,
	"subtotal" 	=> $subtotal,
);

// Create the charge on Stripe's servers - this will charge the user's card
try {
  $charge = \Stripe\Charge::create(array(
    "amount" => $charge_amount, // amount in cents, again
    "currency" => CURRENCY,
    "source" => $token,
    "description" => $description,
    "receipt_email" => $email,
    "metadata" => $metadata
  ));

  $itemquery = $db->query("SELECT * FROM cart WHERE id = '{$cartid}'");
  $itemresults = mysqli_fetch_assoc($itemquery);
  $items = json_decode($itemresults['items'],true);
  foreach ($items as $item) {
  	$newSizes = array();
  	$itemid = $item['id'];
  	$productquery = $db->query("SELECT size FROM products WHERE id = '{$itemid}'");
  	$product = mysqli_fetch_assoc($productquery);
  	$sizes = sizesToArray($product['size']);
  	foreach ($sizes as $size) {
  		if($size['size'] == $item['size']) {
  			$q = $size['quantity'] - $item['quantity'];
  			$newSizes[] = array('size' => $size['size'], 'quantity' => $q);
  		}
  		else {
  			$newSizes[] = array('size' => $size['size'], 'quantity' => $size['quantity']);
  		}
  	}
  	$sizeString = sizesToString($newSizes);
  	$db->query("UPDATE products SET size = '{$sizeString}' WHERE id = '{$itemid}'");
  }
  $db->query("INSERT INTO transactions
  	(charge_id,cartid,full_name,email,street1,street2,city,state,zip,country,subtotal,tax,total,description,trans_type) 
  	VALUES ('$charge->id','$cartid','$full_name','$email','$street1','$street2','$city','$state','$zip','$country','$subtotal','$tax','$total','$description','$charge->object')");

  $db->query("UPDATE cart SET paid = 1 WHERE id = '{$cartid}'");

  $domain = ($_SERVER['HTTP_HOST'] != 'localhost')? '.'.$_SERVER['HTTP_HOST']:false;
  setcookie(CART_COOKIE,'',1,"/",$domain,false);
  include 'includes/head.php';
  include 'includes/navigation.php';

?>

<div class="container">
	<h1 class="text-center text-success">Thank You for your order</h1>
	<p>You have successfully completed your payment. You were charged a total of <?php echo currency($total); ?>. A confirmation email along with a receipt has been sent to your email. This page can also be printed as a receipt.</p>
	<p>Your receipt number is: <strong><?php echo $cartid; ?></strong></p>
	<p>Your order will be shipped to the following address:</p>
	<address>
		<?php echo $full_name; ?><br>
		<?php echo $street1; ?><br>
		<?php echo (($street2 != '')?$street2.'<br>':''); ?><br>
		<?php echo $city. ', '.$state.' ',$zip; ?><br>
		<?php echo $country; ?><br>
	</address>
</div>
	

<?php
} catch(\Stripe\Error\Card $e) {
  // The card has been declined
	echo $e;
}


?>