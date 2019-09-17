<?php
	require 'core/init.php';
	if (!cus_logged_in()) {
		cuslogin_error_redirect();
	}

	include 'includes/head.php';
	include 'includes/navigation.php';
?>

<div class="container">
	<div class="row">
		<table class="table table-bordered table-condensed table-striped table-auto">
			<thead>
				<th>Name</th>
				<th>Description</th>
				<th>Total</th>
				<th>Date</th>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $cusdata['full_name']; ?></td>
					<td><?php echo $cusdata['description']; ?></td>
					<td><?php echo $cusdata['total']; ?></td>
					<td><?php echo $cusdata['trans_date']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>


<?php
	require 'includes/footer.php';
?>