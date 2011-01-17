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

require(dirname(__FILE__).'/../../app/modules/AppKit/lib/AppKit.class.php');
require(dirname(__FILE__).'/../../app/modules/AppKit/lib/class/AppKitBaseClass.class.php');
require(dirname(__FILE__).'/../../app/modules/AppKit/lib/class/AppKitSingleton.class.php');
require(dirname(__FILE__).'/../../app/modules/AppKit/lib/util/AppKitModuleUtil.class.php');

AgaviConfig::set('core.default_context', $env);

// Set uid/gid to www-user/www-group settings from the test.properties config
$test_config = parse_ini_file(AgaviConfig::get("core.root_dir")."/etc/tests/test.properties");
if (function_exists('posix_seteuid') && posix_getuid() == 0) {
	$group = posix_getgrnam($test_config['www-group']);

	if ($group !== false) {
		if (!posix_setegid($group['gid'])) {
		    echo "posix_setegid() failed.\n";
		    return;
		}
	}

	$user = posix_getpwnam($test_config['www-user']);

	if ($user !== false) {
		if (!posix_seteuid($user['uid'])) {
		    echo "posix_seteuid() failed.\n";
		    return;
		}
	}
}

		    // Initialize the appkit framework
PHPUnit_Util_Filter::addDirectoryToFilter(AgaviConfig::get('core.cache_dir'));
AgaviController::initializeModule('Web');
AgaviController::initializeModule('AppKit');
AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');
$ctx = AgaviContext::getInstance();
$ctx->getDatabaseManager()->getDatabase()->connect();
AgaviTesting::dispatch($arguments);

?>
