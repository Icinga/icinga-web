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

	public function testCommandSetup() {
		$console = AgaviContext::getInstance()->getModel('Console.ConsoleInterface',"Api");	
		$lsCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "ls",
				"connection" => $console, 
				"arguments" => array(
					'-la' => '', 
					'1' => '/usr/local/icinga-web/' 
				)
			)
		);
		$grepCmd = AgaviContext::getInstance()->getModel(
			'Console.ConsoleCommand',
			"Api",
			array(
				"command" => "grep",
				"connection" => $console, 
				"arguments" => array(
					'c.*'
				)
			)
		);
		$grepCmd->stdoutFile("/usr/local/icinga/etc/objects/test");
		$lsCmd->pipeCmd($grepCmd);
		$console->exec($lsCmd);
		print_r($lsCmd->getCommandString());
	}
}
