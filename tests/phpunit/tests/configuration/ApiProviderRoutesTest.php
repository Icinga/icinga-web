<?php

class ApiProviderTest extends PHPUnit_Framework_TestCase {
	/**
	* 
	* @dataProvider testDOMProvider
	*
	*/
	public function testProviderAttributesInRouting(AgaviXmlConfigDomDocument $dom,$extRoutes) {
		
		$parser = new AppKitRoutingConfigHandler();
		$parser->execute($dom);
		$toExport = $parser->getApiProviders();
	
		$this->assertEquals(count($toExport),count($extRoutes),"Wrong number of api providers found");
		foreach($extRoutes as $definition) {
			$this->assertContains($definition,$toExport,"Ext.Direct export route not found in routing result set");		
		}
		

	}

	/**
	* @expectedException ApiProviderMissingActionException 
	* @dataProvider testDOMProviderWithoutAction
	* @depends testProviderAttributesInRouting
	**/
	public function testProviderWithoutAction($dom) { 
		$parser = new AppKitRoutingConfigHandler();
		$parser->execute($dom);
	}

	/**
	* @expectedException ApiProviderMissingModuleException
	* @dataProvider testDOMProviderWithoutModule
	* @depends testProviderAttributesInRouting
	**/
	public function testProviderWithoutModule($dom) { 
		$parser = new AppKitRoutingConfigHandler();
		$parser->execute($dom);
	}


	public function testDOMProviderWithoutModule() {
		$dom = '<?xml version="1.0" encoding="UTF-8"?>
<ae:configurations xmlns:ae="http://agavi.org/agavi/config/global/envelope/1.0" xmlns="http://icinga.org/appkit/config/parts/routing/1.0">
	<ae:configuration context="web">
		<routes>
			<route name="route1" pattern="xyz"> <!-- missing module -->
				<route name="s1_route1" action="notExported" pattern="^/test"></route>
				<route name="s2_route1" action="Exported" pattern="^/test2" api_provider="true"></route>
			</route>
		</routes>
	</ae:configuration>
</ae:configurations>
';
		$domDoc = new AgaviXmlConfigDomDocument();
		$domDoc->loadXML($dom);	
		return array(array($domDoc));
	}

	public function testDOMProviderWithoutAction() {
		$dom = '<?xml version="1.0" encoding="UTF-8"?>
<ae:configurations xmlns:ae="http://agavi.org/agavi/config/global/envelope/1.0" xmlns="http://icinga.org/appkit/config/parts/routing/1.0">
	<ae:configuration context="web">
		<routes>
			<route name="route1" module="mod" pattern="xyz"> 				
				<route name="s1_route1" pattern="^/test" api_provider="true"></route> <!-- missing action -->
				<route name="s2_route1" action="Exported" pattern="^/test2" api_provider="true"></route>
			</route>
		</routes>
	</ae:configuration>
</ae:configurations>
';
		$domDoc = new AgaviXmlConfigDomDocument();
		$domDoc->loadXML($dom);	
		return array(array($domDoc));
	}



	public function testDOMProvider() {
		$dom = '<?xml version="1.0" encoding="UTF-8"?>
<ae:configurations xmlns:ae="http://agavi.org/agavi/config/global/envelope/1.0" xmlns="http://icinga.org/appkit/config/parts/routing/1.0">
	<ae:configuration context="web">
		<routes>
			<route name="route1" module="AppKit" pattern="xyz">
				<route name="s1_route1" action="Login" pattern="^/test"></route>
				<route name="s2_route1" action="Logout" pattern="^/test2" api_provider="true"></route>
				<route name="s2_route1" action="Secure" pattern="^/test3" >
					<route name="subtest" pattern="test4" api_provider="true"/> 
				</route>
			</route>
			<route name="route2" module="Web" action="Index" pattern="test" api_provider="true"/>
			<route name="route3" module="Web" action="Index" pattern="test" api_provider="true"/>
		</routes>
	</ae:configuration>
</ae:configurations>
';
		$domDoc = new AgaviXmlConfigDomDocument();
		$domDoc->loadXML($dom);
		$extRoutes = array(
			array("module"=>"AppKit","action"=>"Logout"),	
			array("module"=>"AppKit","action"=>"Secure"),
			array("module"=>"Web","action"=>"Index")
		);
		return array(array($domDoc,$extRoutes));
	}
}
