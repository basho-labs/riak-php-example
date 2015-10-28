<?php

include 'bootstrap.php';

use Basho\Riak;
use Basho\Riak\Api;
use Basho\Riak\Command;

$add = false;
$id = isset($_POST['id']) ? $_POST['id'] : null;
$order_date = isset($_POST['order_date']) ? $_POST['order_date'] : null;
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;
$pickup_date = isset($_POST['pickup_date']) ? $_POST['pickup_date'] : null;
$total = isset($_POST['total']) ? $_POST['total'] : null;

if ($id && $order_date && $quantity && $pickup_date && $total) {
	$result = (new Command\Builder\FetchObject($riak))
	    ->buildLocation($id, BUCKET_CUST, TYPE)
	    ->build()
	    ->execute();

    if ($result->isNotFound()) {
    	header('Location: view.php?id=' . $id);
    }

    $object = $result->getObject();
    $data = $object->getData() ? $object->getData() : new stdClass();
    $data->orders = [];
    $data->orders[$order_date] = [
        'order_date' => $order_date,
        'quantity' => $quantity,
        'pickup_date' => $pickup_date,
        'total' => $total
    ];

    $object->setData($data);

    $result = (new Command\Builder\StoreObject($riak))
    	->withObject($object)
        ->buildLocation($id, BUCKET_CUST, TYPE)
    	->build()
        ->execute();

    if ($result->isSuccess()) {
    	$add = true;
    }
}

header('Location: view.php?id=' . $id);
