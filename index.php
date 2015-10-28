<?php

include 'bootstrap.php';

use Basho\Riak\Command;

$result = (new Command\Builder\Search\FetchObjects($riak))
    ->withQuery('id_s:* OR name_s:* OR email_s:*')
    ->withIndexName(INDEX_CUST)
    ->build()
    ->execute();

$customers = $result->getDocs();
?>
<html>
<head>
</head>
<body>

<div>
<form action="add.php" method="post">
	<input type="hidden" name="add" value="1">
	<input type="email" name="email" placeholder="Email">
	<input type="text" name="name" placeholder="Name">
	<input type="submit" value="create customer">
</form>
</div>

<div>
<table>
<tr>
	<th>Customer ID</th>
	<th>Customer Name</th>
	<th>Customer Email</th>
</tr>
<?php foreach($customers as $customer): ?>
<tr>
	<td><a href="view.php?id=<?=$customer->id_s?>"><?=$customer->id_s?></a></td>
	<td><?=$customer->name_s?></td>
	<td><?=$customer->email_s?></td>
</tr>
<?php endforeach ?>
</table>
</div>
</body>
</html>