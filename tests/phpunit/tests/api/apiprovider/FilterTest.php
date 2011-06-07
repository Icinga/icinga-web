<?php
class TestFilter extends GenericStoreFilter 
{
    public static function parse($filter,$parser) {
        if(!isset($filter["field"]) || !isset($filter["operator"]) || !isset($filter["value"]))
            return null;
        else return new self($filter['field'],$filter['operator'],$filter['value']);
    }
    
    public function initFieldDefinition() {
        $filter1 = new StoreFilterField(); 
        $filter1->displayName = "Testfield";
        $filter1->name = "Testfield_name";
        $filter1->possibleValues = array(StoreFilterField::$VALUE_ANY);
        
        $filter2 = new StoreFilterField(); 
        $filter2->displayName = "Testfield 2";
        $filter2->name = "Testfield_name_sec";
        $filter2->operators = StoreFilterField::$NUMERIC_MODIFIER;
        $filter2->possibleValues = array("test1", "test2");
        $this->addFilterField($filter1);  
        $this->addFilterField($filter2);  
    }
}

class TestDataStoreFilterModifier extends DataStoreFilterModifier { 
    protected $filterClasses = array(
        "TestFilter",
        "GenericStoreFilterGroup"
    );
    
    public function modify(&$o) {
   
    }
}

class FilterTest extends PHPUnit_Framework_TestCase 
{

    public function testFilterCreation() {
        $filter1 = new TestFilter("Testfield_name","=","test1");  
        
        $this->assertEquals(count($filter1->getPossibleFields()),2);
        $this->assertTrue(is_string($filter1->__toString()));
    }
    
    /**
    *
    *  @depends testFilterCreation 
    **/ 
    public function testFilterGroupCreation() {
        $filterGroup = new GenericStoreFilterGroup("or");
        $filter1 = new TestFilter("Testfield_name","=","test1");  
        $filter2 = new TestFilter("Testfield_name_sec","=","test2");  
        $filterGroup2 = new GenericStoreFilterGroup("or");
        
        $filterGroup->addSubFilter($filter1);
        $filterGroup->addSubFilter($filter2);
        $filterGroup2->addSubFilter($filter2);
        $filterGroup->addSubFilter($filterGroup2);
        
        $groupArray = ($filterGroup->__toArray());
       
        $this->assertEquals(
            count($groupArray["items"]),
            3,
            "Not all filters were stored in the filter object"
        );
        $this->assertEquals(
            $groupArray["items"][0],
            $filter1->__toArray(),
            "Wrong filter definition in group"
        );
        $this->assertEquals(
            $groupArray["items"][1],
            $filter2->__toArray(),
            "Wrong filter definition in group"
        );
        $this->assertEquals(
            $groupArray["items"][2],
            $filterGroup2->__toArray(),
            "Wrong filter definition in group"
        ); 
    }
    /**
    * @expectedException InvalidFilterTypeException
    * @depends testFilterGroupCreation
    **/
    public function testInvalidFilterGrouptype() {
        $filterGroup = new GenericStoreFilterGroup("snoobar");
    }
    /**
    * 
    * @depends testInvalidFilterGrouptype
    */
    public function testFilterFromJSON() {
        $json = '{"type":"AND","items":[
            {"field": "Testfield_name","operator": "=","value" : "test1"},
            {"type": "OR", "items":[
                {"field": "Testfield_name" ,"operator": "LIKE" ,"value" : "20" },
                {"field": "Testfield_name_sec" ,"operator": "<","value" : "42" }
            ]}
        ]}';
      
        $testSet = json_decode($json,true);
        $modifier = new TestDataStoreFilterModifier();
        $modifier->handleArgument("filter_json",$testSet);
        $filter = $modifier->getFilter()->__toArray();
        $this->assertEquals($filter['type'],'AND');
        $this->assertEquals(count($filter['items']),2);
        $this->assertEquals($filter['items'][0],$testSet['items'][0]);
        $this->assertEquals($filter['items'][1]['type'],'OR');
        $this->assertEquals(count($filter['items'][1]['items']),2);
        $this->assertEquals($filter['items'][1]['items'][0],$testSet['items'][1]['items'][0]);
        $this->assertEquals($filter['items'][1]['items'][1],$testSet['items'][1]['items'][1]);
    }

   
}
