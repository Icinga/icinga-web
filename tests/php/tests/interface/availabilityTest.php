<?php
/**
 * Checks the avaibility of different interface elements
 * Currently only login is checked
 * 
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
/**
* @depends agaviBootstrapTest::testBootstrap 
*/	
class availabilityTest extends PHPUnit_Framework_TestCase {
			
	public function testShowLoginMask() {
		$ctx = AgaviContext::getInstance('web');
		$container = $ctx->getController()->createExecutionContainer("AppKit","Login.AjaxLogin",null,"html");
		try {
			$result = $container->execute();
			if($result->getHttpStatusCode() != '200' && $result->getHttpStatusCode() != '403' )
				$this->fail("Login mask call failed with status code ".$result->getHttpStatusCode());
		} catch(Exception $e) {
			$this->fail("Login mask threw an exception ".$e->getMessage());	
		}		
	}	
	
	public function testWrongLogin() {
		info("Testing interface avaibility\n");
		info("\tTesting wrong login \n");
		$root = AgaviConfig::get("core.root_dir");
		$ini = parse_ini_file($root."/tests/php/test.properties");
		$params = new AgaviRequestDataHolder();
		$params->setParameters(array(
			"dologin" => 1,
			"password" => "gdsg352sg",
			"username" => "root"
		));
		$json = null;
		$ctx = AgaviContext::getInstance('web');
		$container = $ctx->getController()->createExecutionContainer("AppKit","Login.AjaxLogin",$params,"json","write");
		try {
			$result = $container->execute();
			$json = json_decode($result->getContent(),true);
			if(!$json)	
				throw new Exception("Invalid result given ".$result->getContent());
		} catch(Exception $e) {
			$this->fail("Login threw an exception ".$e->getMessage());	
		}
		
		if($json["success"])
			$this->fail("Login succeeded with wrong credentials. This shouldn't happen");	
		
		success("\tLogin with wrong credentials failed. This is good as it shouldn't be successful. \n");
	}
	
	/**
	 * @depends testShowLoginMask
	 */
	public function testCorrectLogin() {
		info("\tTesting correct login\n");
		$root = AgaviConfig::get("core.root_dir");
		$ini = parse_ini_file($root."/tests/php/test.properties");
		$params = new AgaviRequestDataHolder();
		$params->setParameters(array(
			"dologin" => 1,
			"password" => $ini["testLogin-pass"],
			"username" => $ini["testLogin-name"]			
		));
		
		$ctx = AgaviContext::getInstance('web');
		$container = $ctx->getController()->createExecutionContainer("AppKit","Login.AjaxLogin",$params,"json","write");
		$json = null;
		try {
			$result = $container->execute();
			$json = json_decode($result->getContent(),true);
			if(!$json)	
				throw new Exception("Invalid result given ".$result->getContent());
		} catch(Exception $e) {
			$this->fail("Login threw an exception ".$e->getMessage());	
		}
		if(!$json["success"])
			$this->fail("Login failed with test credentials. Please check the credentials in test.properties");
	
		success("\tLogin successful\n");
	}	

}
