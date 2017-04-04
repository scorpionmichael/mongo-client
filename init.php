<?php

use app\QueryBuilder;

$namespaces = require_once 'file_map.php';

spl_autoload_register(function ($name) {
	global $namespaces;
	
	require_once $namespaces[$name];
});

$queryBuilder = new QueryBuilder($argv[1]);

print_r($queryBuilder->getResult());