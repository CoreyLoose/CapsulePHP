<?php
require __DIR__.'/Connection.php';
require __DIR__.'/MySQL.php';

$connection = new org\capsule\plugins\mysql\Connection(
	$username, $password, $db, $host, $port
);

$lib = new org\capsule\plugins\mysql\MySQL($connection, $capsule->multiArg);