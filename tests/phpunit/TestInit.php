<?php

class IcingaWebTestTool {
    
    const PROPERTIES_FILE = "tests/phpunit/test.properties";
    
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
    
    /**
     * Loads the ini file with test properties to make it
     * project wide available for testing
     * @throws Exception
     * @return array Array of parsed properties
     */
    private static function parseTestProperties() {
        $file = self::getRootPath(). '/'. self::PROPERTIES_FILE;
        self::$properties = parse_ini_file($file);
        
        if (!is_array(self::$properties)) {
            throw new Exception('Propertiesfile '. $file. ' not found!');
        }
        
        return self::$properties;
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

 	/**
	*	Asserts that $actual is of instance $expected, use instead of the one defined
	*   in PHPUnit_Framework_Assert for compatibility reasons (doesn't exist prior to v3.5)
	*	Fails with optional $message if instance doesn't match
	*   If phpUnit provides an assertInstanceOf class (>= v.3.5) this one will be used, 
	*	otherwise it will be checked directly here. 
	*	@param $expected 	The expected Instance
	*	@param $actual		The object to test
	*	@param $message		The message to return if assertion fails (optional)
	*/
	public static function assertInstanceOf($expected,$actual,$message = '') {
		if(method_exists('PHPUnit_Framework_Assert','assertInstanceOf')) {
			PHPUnit_Framework_Assert::assertInstanceOf($expected,$actual,$message);
		} else {
			$constraint;
			if (is_string($expected)) {
				if (class_exists($expected) || interface_exists($expected)) {
					$constraint = new PHPUnit_Framework_Constraint_IsInstanceOf(
					  $expected
					);
				}

				else {
					throw PHPUnit_Util_InvalidArgumentHelper::factory(
					  1, 'class or interface name'
					);
				}
			} else {
            	throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'string');
    	    }
		

	        if (!$constraint->evaluate($actual)) {
    	        $constraint->fail($actual, $message);
    	    } 
		}
	}   
}

class IcingaWebTestBootstrap {
    
    /**
     * Starts an agavi context for testing purposes. This was bundled into the
     * test bootstrap method to call this only once
     * @param string $env	Name of the context
     * @return AgaviContext	The created context
     */
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

