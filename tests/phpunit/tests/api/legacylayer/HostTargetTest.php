<?php

class LegacyLayerHostTargetTests extends PHPUnit_Framework_TestCase {
    
    protected function createSearch() {
        return AgaviContext::getInstance()->getModel("Store.LegacyLayer.IcingaApi","Api"); 
    }    
    
    protected function createDeprecatedSearch() {
       
    }
    

    public function testSimpleHostRead() {	
        $search = $this->createSearch();       
        $search->setResultType("ARRAY"); 
        $search->setSearchTarget(IcingaApiConstants::TARGET_HOST);
        $search->setFields(array(
            "HOST_DISPLAY_NAME",
            "HOST_NAME",
            "HOST_ALIAS",
            "HOST_STATE",
            "HOST_IS_PENDING",
            "CONTACT_NAME",
            "CONTACT_CUSTOMVARIABLE_NAME" 
        ),true);
        $result = $search->doRead(); 
//        print_r($result["data"]);
    }
    
}
