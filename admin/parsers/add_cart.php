<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php';
	$product_id = checkInput($_POST['product_id']);
	$size = checkInput($_POST['size']);
	$available = checkInput($_POST['available']);
	$quantity = checkInput($_POST['quantity']);
	$item = array();
	$item[] = array(
		'id' => $product_id,
		'size' => $size,
		'quantity' => $quantity,
	);

	$domain = false;
	$query = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
	$product = mysqli_fetch_assoc($query);
	$_SESSION['success_flash'] = $product['title']. ' has been added to cart.';

	// Check if cart cookie exists.
	if ($cartid != '') {
		$cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cartid}'");
		$cart = mysqli_fetch_assoc($cartQ);
		$previtems = json_decode($cart['items'],true);
		$item_match = 0;
		$newitems = array();
		foreach ($previtems as $pitem) {
			if ($item[0]['id'] == $pitem['id'] && $item[0]['size'] == $pitem['size']) {
				$pitem['quantity'] = $pitem['quantity'] + $item[0]['quantity'];

				if ($pitem['quantity'] > $available) {
					$pitem['quantity'] = $available;
				}
				$item_match = 1;
			}
			$newitems[] = $pitem;
		}
		if ($item_match != 1) {
			$newitems = array_merge($item,$previtems);
		}
		$itemsjson = json_encode($newitems);
		$cartexpire = date("Y-m-d H:i:s",strtotime("+30 days"));
		$db->query("UPDATE cart SET items = '{$itemsjson}', expire_date = '{$cartexpire}' WHERE id = '{$cartid}'");
		setcookie(CART_COOKIE,'',1,"/",$domain,false);
		setcookie(CART_COOKIE,$cartid,CART_COOKIE_EXPIRE,'/',$domain,false);
	}
	else {
		// Add cart to database and set cookie.
		$itemsjson = json_encode($item);
		$cartexpire = date("Y-m-d H:i:s",strtotime("+30 days"));
		$db->query("INSERT INTO cart (items,expire_date) VALUES ('{$itemsjson}','{$cartexpire}')");
		$cartid = $db->insert_id;
		setcookie(CART_COOKIE,$cartid,CART_COOKIE_EXPIRE,'/',$domain,false);
	}

?>