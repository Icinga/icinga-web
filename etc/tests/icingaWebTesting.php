#! /usr/bin/php
<?php

require(dirname(__FILE__).'/../../lib/agavi/src/testing.php');
require(dirname(__FILE__).'/../../lib/doctrine/lib/Doctrine.php');

spl_autoload_register("Doctrine::autoload");
require('config.php');

$arguments = AgaviTesting::processCommandlineOptions(); 
if(!$argv)
	throw new Exception("Please run this tests from the commandline");

function info($str) {
	print("\x1b[2;34m".$str."\x1b[m");
}
function success($str) {
	print("\x1b[2;32m".$str."\x1b[m");
}
function error($str) {
	print("\x1b[2;31m".$str."\x1b[m");
}

function stdin($prompt = "", $args = array(),$default=null) {
	$inp = fopen("php://stdin","r");
	$result;
	$argsString = (!empty($args) ? '['.implode("/",$args).']' : '');
	$defString = ($default ? "($default)" : '');
	$error = false;
	do {
		$error = false;
		// get input
		echo $prompt." ".$argsString." ".$defString;
		$result = fscanf($inp,"%s\n");	
		
		if(!empty($args) && !in_array($result[0],$args,true))
			$error = true;
	} while($error);
	
	return $result[0];
}


if(isset($arguments['environment'])) {
	$env = $arguments['environment'];
	unset($arguments['environment']);
} else {
	$env = 'testing';
}

AgaviTesting::bootstrap($env);
AgaviConfig::set('core.default_context', $env);
// Initialize the appkit framework
PHPUnit_Util_Filter::addDirectoryToFilter(AgaviConfig::get('core.cache_dir'));
AgaviController::initializeModule('Web');
AgaviController::initializeModule('AppKit');
AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');
$ctx = AgaviContext::getInstance();
$ctx->getDatabaseManager()->getDatabase()->connect();
AgaviTesting::dispatch($arguments);

?>