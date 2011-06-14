<?php
class IcingaApiDatastoreTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleRead()    {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields','*');
        
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
            "request" => $req
        ));
        $this->assertEquals(count($dataStore->doRead()),30);  
    }



    public function testMultiRelationRead() {
       
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields',array('h.host_id','s.*'));
        $req->setParameter('joins',array(
            array(
                "type"=>"inner",
                "src"=>"h", 
                "relation" => "services", 
                "alias" =>"s"
            )
        ));
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req
        ));
        $result = $dataStore->doRead();
        foreach($result as $host) {  
            $this->assertNotEquals($host->services->display_name,"");
        }
    }
    /**
    * @expectedException InvalidArgumentException
    *
    **/
    public function testInvalidMultiRelationReadWithScalar() {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields',array('h.host_id','s.*'));
        $req->setParameter('joins',"InvalidScalarRequest");
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req
        ));
        $result = $dataStore->doRead();
    }

    /**
    * @expectedException InvalidArgumentException
    *
    **/
    public function testInvalidMultiRelationReadWithMissingSrc() {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields',array('h.host_id','s.*'));
        $req->setParameter('joins',array(array("relation"=> "services","alias" => "s")));
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req
        ));
        $result = $dataStore->doRead();
    }

    public function testFilter()    {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields','h.*');  
        $req->setParameter('filter_json',array(
            "type"=>"OR",
            "items"=>array(
                array("field"=>"display_name","operator"=>"=","value"=>"c1-db1"),
                array("type"=>"AND", "items"=>array(
                        array(
                            "field" => "display_name",
                            "operator"=>"LIKE",
                            "value" => "c2%"
                        ),array(
                            "field" => "display_name",
                            "operator"=>"IN",
                            "value" => array("c2-proxy","c2-mail-1")
                        )
                    )
                )
            )
        ));
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req
        ));
        $recordCollection = $dataStore->doRead();
        $checkArr = array(
            "c2-proxy"  =>  true,
            "c2-mail-1" =>  true,
            "c1-db1"    =>  true
        ); 
        foreach($recordCollection as $record) {
            $name = $record["display_name"];
            
            $this->assertTrue($checkArr[$name]);
            $checkArr[$name] = false;
        }
        foreach($checkArr as $name=>$val)
            $this->assertFalse($val); 
    }
    public function testOrder()  {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields','h.display_name');  
        $req->setParameter('sortfield','h.display_name');
        $req->setParameter('dir','DESC');
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req,
           "resultType" => "ARRAY"

        ));
        $recordCollection = $dataStore->doRead();
        for($i=1;$i<count($recordCollection);$i++) {
            $current = $recordCollection[$i];
            $this->assertGreaterThanOrEqual($current["display_name"],$recordCollection[$i-1]["display_name"]);
        }
        
        $req->setParameter('dir','ASC');
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req,
           "resultType" => "ARRAY"

        ));
        $recordCollection = $dataStore->doRead();
        for($i=1;$i<count($recordCollection);$i++) {
            $current = $recordCollection[$i];
            $this->assertLessThanOrEqual($current["display_name"],$recordCollection[$i-1]["display_name"]);
        } 
    }
     
    public function testLimit()  {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields','h.display_name');  
        $req->setParameter('limit',5);
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req,
           "resultType" => "ARRAY"
        ));
        $firstResult = $dataStore->doRead();
        $this->assertEquals(count($firstResult),5);
        $req->setParameter('offset',4);
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req,
           "resultType" => "ARRAY"
        ));
        $result = $dataStore->doRead();
        $this->assertEquals($result[0],$firstResult[4]);
    }

    public function testCombined()  {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts h');
        $req->setParameter('fields','h.display_name,s.*'); 
        $req->setParameter('joins',array(array("src"=>"h","relation"=> "services","alias" => "s")));
        $req->setParameter('limit',5);
        $req->setParameter('sortfield','h.display_name');
        $req->setParameter('dir','DESC');       
        $req->setParameter('filter_json',array(
            "type"=>"OR",
            "items"=>array(
                array("field"=>"h.display_name","operator"=>"=","value"=>"c1-db1"),
                array("type"=>"OR", "items"=>array(
                        array(
                            "field" => "h.display_name",
                            "operator"=>"LIKE",
                            "value" => "%mail%"
                        ),array(
                            "field" => "h.display_name",
                            "operator"=>"IN",
                            "value" => array("c2-db1")
                        )
                    )
                )
            )
        ));
        $dataStore = $ctx->getModel('Store.IcingaApiDataStore','Api',array(
           "request" => $req,
           "resultType" => "ARRAY"
        ));
        $firstResult = $dataStore->doRead();
        print_r($firstResult); 
    }
    
}
