<?php

include 'bootstrap.php';

use Basho\Riak\Command;

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
	header('Location: view.php?view_error=1');
}

$result = (new Command\Builder\FetchObject($riak))
	->withDecodeAsAssociative()
    ->buildLocation($id, BUCKET_CUST, TYPE)
    ->build()
    ->execute();

if ($result->isSuccess() && $result->getObject()) {
	$customer = $result->getObject()->getData();
}
?>
<html>
<head>
</head>
<body>

<div>
Customer #: <?=$customer['id_s']?><br>
Name: <?=$customer['name_s']?><br>
Email: <?=$customer['email_s']?><br>
</div>

<br>
<a href="index.php">back</a>
<br>

<h2>Orders</h2>
<div>
<form action="add_order.php" method="post">
	<input type="hidden" name="add" value="1">
	<input type="hidden" name="id" value="<?=$id?>">
	Order Date: <input type="date" name="order_date">
	Qty: <input type="number" name="quantity" placeholder="quantity">
	Pickup Date: <input type="date" name="pickup_date">
	<input type="number" name="total" placeholder="total">
	<input type="submit" value="create order">
</form>
</div>

<div>
<table>
<tr>
	<th>Order Date</th>
	<th>Quantity</th>
	<th>Pickup Date</th>
	<th>Total</th>
</tr>
<?php foreach ($customer['orders'] as $date => $order) {?>
<tr>
	<td><?=$order['order_date']?></td>
	<td><?=$order['quantity']?></td>
	<td><?=$order['pickup_date']?></td>
	<td>$<?=$order['total']?></td>
</tr>
<?php } ?>
</table>
</div>
</body>
</html>