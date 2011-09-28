<?php

class ApiValidationParserTest extends PHPUnit_Framework_TestCase {
    /**
    * @dataProvider testActionModuleProvider
    * @group Configuration
    */
    public function testProviderAttributesInRouting($action,$module) {
    	$parser = new AppKitApiProviderParser();
    	$parser->execute(array(array("action"=>$action,"module"=>$module))); 
    }
    	
    /** 
    * @group Configuration
    */
    public function testActionModuleProvider() {
        return array(
            array("ApiSearch","Api")
        );
    }	
}

