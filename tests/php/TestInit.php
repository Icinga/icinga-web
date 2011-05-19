<?php

class IcingaWebTestTool {
    
    private static $path_test = null;
    private static $path_root = null;
    
    private static $properties = null;
    
    public static function initialize() {
        self::$path_test = __DIR__;
        self::$path_root = dirname(dirname(__DIR__));
        self::parseTestProperties();
    }
   
    public static function getRootPath() {
        return self::$path_root;
    }
    
    public static function getTestPath() {
        return self::$path_test;
    }
    
    private static function parseTestProperties() {
        $file = self::getRootPath(). "/tests/php/test.properties";
        return self::$properties = parse_ini_file($file);
    }
    
    public static function getProperties() {
        return self::$properties;
    }
    
    public static function getProperty($name, $default=null) {
        if (array_key_exists($name, self::$properties)) {
            return self::$properties[$name];
        }
        
        return $default;
    }
    
}

class IcingaWebTestBootstrap {
    
    public function bootstrapAgavi($env='testing') {
        
        require IcingaWebTestTool::getRootPath(). '/lib/agavi/src/agavi.php';
        
    	AgaviConfig::set('core.testing_dir', IcingaWebTestTool::getTestPath());
    	
    	AgaviConfig::set('core.app_dir', IcingaWebTestTool::getRootPath(). DIRECTORY_SEPARATOR. 'app');
    	
    	AgaviConfig::set('core.root_dir', IcingaWebTestTool::getRootPath());
    	
    	Agavi::bootstrap($env);
    	
    	AgaviConfig::set('core.default_context', $env);
    	
    	AppKitAgaviUtil::initializeModule('AppKit');
    	
    	AgaviConfig::set('core.context_implementation', 'AppKitAgaviContext');
    	
    	return AgaviContext::getInstance($env);
    }
    
}

IcingaWebTestTool::initialize();
IcingaWebTestBootstrap::bootstrapAgavi();

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

