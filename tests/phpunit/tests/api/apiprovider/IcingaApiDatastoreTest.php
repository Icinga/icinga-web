<?php
class IcingaApiDatastoreTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleRead()    {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','host');
        $req->setParameter('fields','*');
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
            "request" => $req
        ));
        print_r($dataStore->doRead());
    }

    public function testMultiRelationRead() {
        $this->markTestIncomplete("Not implemented yet");
    }
    
    public function testFilter()    {
        $this->markTestIncomplete("Not implemented yet");
    }
    public function testOrder()  {
        $this->markTestIncomplete("Not implemented yet");
    } 
    public function testLimit()  {
        $this->markTestIncomplete("Not implemented yet");
    }
    public function testCombined()  {
        $this->markTestIncomplete("Not implemented yet");
    }
}
