<?php

include 'bootstrap.php';

use Basho\Riak;
use Basho\Riak\Api;
use Basho\Riak\Command;

$add = false;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$id = !empty($email) ? substr(md5($email), 0, 5) : '';

if ($name && $email) {
	$result = (new Command\Builder\FetchObject($riak))
	    ->buildLocation($id, BUCKET_CUST, TYPE)
	    ->build()
	    ->execute();

    $object = new Riak\Object(['id_s' => $id, 'email_s' => $email, 'name_s' => $name]);
    $object->setContentType(Api\Http::CONTENT_TYPE_JSON);

    if (!$result->isNotFound() && null !== $result->getObject()->getVclock()) {
    	$object->setVclock($result->getObject()->getVclock());
    }

    $result = (new Command\Builder\StoreObject($riak))
    	->withObject($object)
        ->buildLocation($id, BUCKET_CUST, TYPE)
    	->build()
        ->execute();

    if ($result->isSuccess()) {
    	$add = true;
    }
}

header('Location: index.php?add=' . $add);
