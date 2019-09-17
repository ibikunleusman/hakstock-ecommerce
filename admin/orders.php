<?php
	require '../core/init.php';
	if(!is_logged_in()) {
		header('Location: login.php');
	}
	include 'includes/head.php';
	include 'includes/navigation.php';
	$currentdate=getdate(date("U"));

	// complete order
	if (isset($_GET['complete']) && $_GET['complete'] == 1) {
		$cartid = checkInput((int)$_GET['cartid']);
		$db->query("UPDATE cart SET shipped = 1 WHERE id = '{$cartid}'");
		$_SESSION['success_flash'] = "Order has been completed.";
		header('Location: index.php');
	}

	$trans_id = checkInput((int)$_GET['trans_id']);
	$transQuery = $db->query("SELECT * FROM transactions WHERE id = '{$trans_id}'");
	$trans = mysqli_fetch_assoc($transQuery);
	$cartid = $trans['cartid'];
	$cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cartid}'");
	$cart = mysqli_fetch_assoc($cartQ);
	$items = json_decode($cart['items'],true);
	$idArray = array();
	$products = array();
	foreach ($items as $item) {
		$idArray[] = $item['id'];
	}
	$ids = implode(',',$idArray);
	$productQ = $db->query(
		"SELECT i.id as 'id', i.title as 'title', c.id as 'cid', c.category as 'child', p.category as 'parent' 
		FROM products i
		LEFT JOIN categories c ON i.categories = c.id
		LEFT JOIN categories p ON c.parent = p.id
		WHERE i.id IN ({$ids})
	");
	while ($p = mysqli_fetch_assoc($productQ)) {
		foreach ($items as $item) {
			if ($item['id'] == $p['id']) {
				$x = $item;
				continue;
			}
		}
		$products[] = array_merge($x,$p);
	}
?>
<hr>
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>

<h3 style="margin-left: 30px;" class="text-center">Items Ordered</h3>
<table class="table table-bordered table-condensed table-striped table-auto">
	<thead>
		<th>Title</th>
		<th>Category</th>
		<th>Size</th>
		<th>Quantity</th>
	</thead>
	<tbody>
		<?php foreach($products as $product): ?>
			<tr>
				<td><?php echo $product['title']; ?></td>
				<td><?php echo $product['parent'].' > '.$product['child']; ?></td>
				<td><?php echo $product['size']; ?></td>
				<td><?php echo $product['quantity']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<br>
<br>

<div class="container">
	<div class="row">
		<div class="col-md-6">
			<h4 class="text-center">Order Details</h4>
			<table class="table table-condensed table-auto">
				<tbody>
					<tr>
						<td>Sub-Total</td>
						<td><?php echo currency($trans['subtotal']); ?></td>
					</tr>
					<tr>
						<td>Tax</td>
						<td><?php echo currency($trans['tax']); ?></td>
					</tr>
					<tr>
						<td>Total</td>
						<td><?php echo currency($trans['total']); ?></td>
					</tr>
					<tr>
						<td>Order Date</td>
						<td><?php echo $trans['trans_date'] ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-md-6">
			<h4 class="text-center">Shipping Address</h4>
			<address>
				<?php echo $trans['full_name']; ?><br>
				<?php echo $trans['street1']; ?><br>
				<?php echo ($trans['street2'] != '')?$trans['street2'].'<br>':''; ?>
				<?php echo $trans['city'].', '.$trans['state'].' '.$trans['zip']; ?><br>
				<?php echo $trans['country']; ?><br>
			</address>
		</div>
		<div class="pull-right">
			<a href="index.php" class="ghost-button">Cancel</a>
			<a href="orders.php?complete=1&cartid=<?=$cartid;?>" class="ghost-button">Complete Order</a>
		</div>
	</div>

</div>


<?php
	include 'includes/footer.php';
?>