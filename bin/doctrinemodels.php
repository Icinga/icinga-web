#!/usr/bin/php
<?php

require_once(dirname(dirname(__FILE__)). '/lib/doctrine/lib/Doctrine.php');

spl_autoload_register(array('Doctrine', 'autoload'));

$stdin = fopen('php://stdin', 'r');

$queries = array ('db_host', 'db_name', 'db_user', 'db_passwd', 'target_dir');
foreach ($queries as $var) ${ $var } = null;

foreach ($queries as $var) {
	
	echo '$'. $var. ':';
	${ $var } = trim(fgets($stdin));
	
}

if (!file_exists($target_dir)) {
	mkdir($target_dir, 0755, true);
}

echo "\nDUMP:\n";
foreach ($queries as $var) echo "- \$$var=". ${ $var }. chr(10);
echo "\n";

Doctrine_Manager::connection("mysql://$db_user:$db_passwd@$db_host/$db_name", 'default');
Doctrine::generateModelsFromDb($target_dir, array('default'), array('generateTableClasses' => true));

fclose($stdin);

?>