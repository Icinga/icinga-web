<?php

class ApiValidationParserTest extends PHPUnit_Framework_TestCase {
    /**
    * 
    * @dataProvider testActionModuleProvider
    *
    */
    public function testProviderAttributesInRouting($action,$module) {
    	$parser = new AppKitApiProviderParser();
    	$parser->execute(array(array("action"=>$action,"module"=>$module))); 
    }	

    public function testActionModuleProvider() {
        return array(
            array("ApiSearch","Api")
        );
    }	
}
?>
