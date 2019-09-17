<?php
	require_once '../core/init.php';
	if (!is_logged_in()) {
		header('Location: login.php');
	}
	include 'includes/head.php';
	include 'includes/navigation.php';
	$currentdate=getdate(date("U"));
?>

<?php
	$transquery = "SELECT t.id, t.cartid, t.full_name, t.description, t.trans_date, t.total, c.items, c.paid, c.shipped 
				FROM transactions t
				LEFT JOIN cart c ON t.cartid = c.id
				WHERE c.paid = 1 AND c.shipped = 0
				ORDER BY t.trans_date";
	$transResults = $db->query($transquery);
?>

<hr>
<p class="text-center"><?php echo "$currentdate[weekday], $currentdate[month] $currentdate[mday], $currentdate[year]"; ?></p>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h3 style="margin-left: 40px">Orders To Ship</h3>
			<hr>
			<table class="table table-bordered table-condensed table-striped table-auto">
				<thead>
					<th>Name</th>
					<th>Description</th>
					<th>Total</th>
					<th>Date</th>
					<th></th>
				</thead>
				<tbody>
					<?php while($order = mysqli_fetch_assoc($transResults)): ?>
						<tr>
							<td><?php echo $order['full_name']; ?></td>
							<td><?php echo $order['description']; ?></td>
							<td><?php echo $order['total']; ?></td>
							<td><?php echo $order['trans_date']; ?></td>
							<td><a href="orders.php?trans_id=<?php echo $order['id']?>">Details</a></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>

	<br>
	<br>
	<div class="row">
		<!-- Sales By Month -->
		<?php
			$currentyear = date("Y");
			$prevyear = $currentyear - 1;
			$currentyearQ = $db->query("SELECT total, trans_date FROM transactions WHERE YEAR(trans_date) = '{$currentyear}'");
			$prevyearQ = $db->query("SELECT total, trans_date FROM transactions WHERE YEAR(trans_date) = '{$prevyear}'");
			$current = array();
			$previous = array();
			$currentTotal = 0;
			$prevTotal = 0;
			while ($x = mysqli_fetch_assoc($currentyearQ)) {
				$month = date("m",strtotime($x['trans_date']));
				$month = (int)$month;
				if (!array_key_exists($month,$current)) {
					$current[(int)$month] = $x['total'];
				}
				else {
					$current[(int)$month] += $x['total'];
				}
				$currentTotal += $x['total'];
			}

			while ($y = mysqli_fetch_assoc($prevyearQ)) {
				$month = date("m",strtotime($y['trans_date']));
				$month = (int)$month;
				if (!array_key_exists($month,$current)) {
					$previous[(int)$month] = $y['total'];
				}
				else {
					$previous[(int)$month] += $y['total'];
				}
				$prevTotal += $y['total'];
			}
		?>
		<div class="col-md-4">
			<h4 class="text-center">Sales By Month</h4>
			<table class="table table-bordered table-condensed table-striped">
				<thead>
					<th></th>
					<th><?php echo $prevyear; ?></th>
					<th><?php echo $currentyear; ?></th>
				</thead>
				<tbody>
					<?php for($i=1;$i<=12;$i++): 
						$dt = DateTime::createFromFormat('!m',$i);
					?>
						<tr<?php echo (date("m") == $i)?' class="info"':''; ?>>
							<td><?php echo $dt->format("F"); ?></td>
							<td><?php echo (array_key_exists($i,$previous))?currency($previous[$i]):currency(0); ?></td>
							<td><?php echo (array_key_exists($i,$current))?currency($current[$i]):currency(0); ?></td>
						</tr>
					<?php endfor; ?>
					<tr>
						<td>Total</td>
						<td><?php echo currency($prevTotal); ?></td>
						<td><?php echo currency($currentTotal); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Inventory -->
		<?php
			$inventQ = $db->query("SELECT * FROM products WHERE removed = 0");
			$lowitems = array();
			while($product = mysqli_fetch_assoc($inventQ)){
				$item = array();
				$sizes = sizesToArray($product['size']);
				foreach ($sizes as $size) {
					if ($size['quantity'] <= $size['quantity']) {
						$cat = get_category($product['categories']);
						$item = array(
							'title'		=> 	$product['title'],
							'size'		=> 	$size['size'],
							'quantity'	=>	$size['quantity'],
							'threshold'	=>	$size['threshold'],
							'category'	=>	$cat['parent'] . ' > '.$cat['child']
						);
						$lowitems[] = $item;
					}				
				}
			}
		?>
		<div class="col-md-8">
			<h4 class="text-center">Low Inventory</h4>
			<table class="table table-condensed table-striped table-bordered">
				<thead>
					<th>Product</th>
					<th>Category</th>
					<th>Size/Color</th>
					<th>Quantity</th>
					<th>Threshold</th>
				</thead>
				<tbody>
					<?php foreach($lowitems as $item): ?>
						<tr<?php echo ($item['quantity'] == 0)?' class="danger"':''; ?>>
							<td><?php echo $item['title']; ?></td>
							<td><?php echo $item['category']; ?></td>
							<td><?php echo $item['size']; ?></td>
							<td><?php echo $item['quantity']; ?></td>
							<td><?php echo $item['threshold']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>



<?php  
	include 'includes/footer.php';
?>