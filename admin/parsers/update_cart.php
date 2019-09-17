<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php';
	$mode = checkInput($_POST['mode']);
	$editid = checkInput($_POST['editid']);
	$editsize = checkInput($_POST['editsize']);
	$cartquery = $db->query("SELECT * FROM cart WHERE id = '{$cartid}'");
	$result = mysqli_fetch_assoc($cartquery);
	$items = json_decode($result['items'],true);
	$updateditems = array();
	$domain = false;

	if ($mode == 'less') {
		foreach ($items as $item) {
			if ($item['id'] == $editid && $item['size'] == $editsize) {
				$item['quantity'] = $item['quantity'] - 1;
			}
			if ($item['quantity'] > 0) {
				$updateditems[] = $item;
			}
		}
	}

	if ($mode == 'add') {
		foreach ($items as $item) {
			if ($item['id'] == $editid && $item['size'] == $editsize) {
				$item['quantity'] = $item['quantity'] + 1;
			}
			$updateditems[] = $item;
		}
	}

	if (!empty($updateditems)) {
		$updatejson = json_encode($updateditems);
		$db->query("UPDATE cart SET items = '{$updatejson}' WHERE id = '{$cartid}'");
		$_SESSION['success_flash'] = 'Shopping cart has been updated.';
	}

	if (empty($updateditems)) {
		$db->query("DELETE FROM cart WHERE id = '{$cartid}'");
		setcookie(CART_COOKIE,'',1,"/",$domain,false);
	}

?>