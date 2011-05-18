<?php

define('TEST_PATH', __DIR__);
define('ROOT_PATH', dirname(dirname(__DIR__)));

ob_start();

function info($str) {
	//print("\x1b[2;34m".$str."\x1b[m");
}
function success($str) {
	//print("\x1b[2;32m".$str."\x1b[m");
}
function error($str) {
	//print("\x1b[2;31m".$str."\x1b[m");
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

