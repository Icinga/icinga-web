<?php

class ConsoleInterfaceTest extends PHPUnit_Framework_TestCase {
	
	public function testInterfaceInstance() {
		// default
		AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api");
		// specific host
		AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>"localhost")); 
	}
	
	/**
     * @expectedException ApiUnknownHostException
     */
	public function testUnknownHostInstance() {
		$model = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api",array("host"=>"dgjksdd"));
	}
}