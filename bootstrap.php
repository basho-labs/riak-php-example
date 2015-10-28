<?php

include 'vendor/autoload.php';

use Basho\Riak\Command;

// using the same bucket type as the client library test suite
const TYPE = 'phptest_search';
const INDEX_CUST = 'store_customers_idx';
const BUCKET_CUST = 'store_Customers';

$node = (new \Basho\Riak\Node\Builder())
    ->atHost('riak-test')
    ->onPort(8098)
    ->build();

$riak = new \Basho\Riak([$node]);

$response = (new Command\Builder\Search\FetchIndex($riak))
    ->withName(INDEX_CUST)
    ->build()
    ->execute();

if ($response->getCode() <> '200') {
	$response = (new Command\Builder\Search\StoreIndex($riak))
	    ->withName(INDEX_CUST)
	    ->usingSchema('_yz_default')
	    ->build()
	    ->execute();

	if (!$response->isSuccess()) {
		throw new Exception('StoreIndex failed');
	}

    $response = (new Command\Builder\Search\AssociateIndex($riak))
        ->withName(INDEX_CUST)
        ->buildBucket(BUCKET_CUST, TYPE)
        ->build()
        ->execute();

	if (!$response->isSuccess()) {
		throw new Exception('AssociateIndex failed.');
	}
}
