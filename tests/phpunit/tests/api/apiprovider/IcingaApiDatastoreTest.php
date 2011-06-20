<?php
class IcingaApiDatastoreTest extends PHPUnit_Framework_TestCase
{
    /**
    *
    * @dataProvider storeModelProvider
    **/
    public function testSimpleRead($model)    {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields','*');
        
        $dataStore = $ctx->getModel($model,'Api',array(
            "request" => $req
        ));
        $this->assertEquals(count($dataStore->doRead()),30);  
    }

    /**
    *
    * @dataProvider storeModelProvider
    **/
    public function testMultiRelationRead($model) {
       
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields',array('host_id','s.display_name','ss.output'));
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req,
            "resultType" => "ARRAY"
        ));
        $result = $dataStore->doRead();
        foreach($result as $host) {    
            $this->assertNotEquals($host["services"][0]["display_name"],"");
        }
    }

    /**
    * @expectedException InvalidArgumentException
    * @dataProvider      storeModelProvider
    **/
    public function testInvalidMultiRelationReadWithScalar($model) {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields',array('host_id','s.*'));
        $req->setParameter('joins',"InvalidScalarRequest");
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req
        ));
        $result = $dataStore->doRead();
    }

    /**
    * @expectedException InvalidArgumentException
    * @dataProvider      storeModelProvider
    **/
    public function testInvalidMultiRelationReadWithMissingSrc($model) {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields',array('host_id','s.*'));
        $req->setParameter('joins',array(array("relation"=> "services","alias" => "s")));
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req
        ));
        $result = $dataStore->doRead();
    }

    /**
    *
    * @dataProvider      storeModelProvider
    */
    public function testFilter($model)    {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields','*');  
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
        $dataStore = $ctx->getModel($model,'Api',array(
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

    /**
    * @dataProvider      storeModelProvider
    */
    public function testOrder($model)  {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields','display_name');  
        $req->setParameter('sortfield','display_name');
        $req->setParameter('dir','DESC');
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req,
           "resultType" => "ARRAY"

        ));
        $recordCollection = $dataStore->doRead();
        for($i=1;$i<count($recordCollection);$i++) {
            $current = $recordCollection[$i];
            $this->assertGreaterThanOrEqual($current["display_name"],$recordCollection[$i-1]["display_name"]);
        }
        
        $req->setParameter('dir','ASC');
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req,
           "resultType" => "ARRAY"

        ));
        $recordCollection = $dataStore->doRead();
        for($i=1;$i<count($recordCollection);$i++) {
            $current = $recordCollection[$i];
            $this->assertLessThanOrEqual($current["display_name"],$recordCollection[$i-1]["display_name"]);
        } 
    }
    
    /**
    * @dataProvider      storeModelProvider
    */
    public function testLimit($model)  {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields','display_name');  
        $req->setParameter('limit',5);
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req,
           "resultType" => "ARRAY"
        ));
        $firstResult = $dataStore->doRead();
        $this->assertEquals(count($firstResult),5);
        $req->setParameter('offset',4);
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req,
           "resultType" => "ARRAY"
        ));
        $result = $dataStore->doRead();
        $this->assertEquals($result[0],$firstResult[4]);
    }

    /**
    * @dataProvider      storeModelProvider
    */
    public function testCombined($model)  {
        $ctx = AgaviContext::getInstance();
        $req = new AgaviRequestDataHolder();
        $req->setParameter('target','IcingaHosts');
        $req->setParameter('fields','display_name,s.display_name'); 
        $req->setParameter('limit',5);
        $req->setParameter('sortfield','display_name');
        $req->setParameter('dir','DESC');       
        $req->setParameter('filter_json',array(
            "type"=>"OR",
            "items"=>array(
                array("field"=>"display_name","operator"=>"=","value"=>"c1-db1"),
                array("type"=>"OR", "items"=>array(
                        array(
                            "field" => "display_name",
                            "operator"=>"LIKE",
                            "value" => "%mail%"
                        ),array(
                            "field" => "display_name",
                            "operator"=>"IN",
                            "value" => array("c2-db1")
                        )
                    )
                )
            )
        ));
        $dataStore = $ctx->getModel($model,'Api',array(
           "request" => $req,
           "resultType" => "ARRAY"
        ));
        $firstResult = $dataStore->doRead();
        $this->assertLessThanOrEqual(5,count($firstResult)); 
    }

    public function storeModelProvider() {
        return array(
            array('Store.HostStore',"Api")
        );
    }    
}
