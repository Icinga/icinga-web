<?php
class TestFilter extends GenericStoreFilter 
{
    public function initFieldDefinition() {
        $filter1 = new StoreFilterField(); 
        $filter1->displayName = "Testfield";
        $filter1->name = "Testfield_name";
        $filter1->possibleValues = array(StoreFilterField::$VALUE_ANY);
        
        $filter2 = new StoreFilterField(); 
        $filter2->displayName = "Testfield 2";
        $filter2->name = "Testfield_name_sec";
        $filter2->possibleValues = array("test1", "test2");
        $this->addFilterField($filter1);  
        $this->addFilterField($filter2);  
    }
}


class FilterTest extends PHPUnit_Framework_TestCase 
{

    public function testFilterCreation() {
        $filter1 = new TestFilter("Testfield_name","=","test1");  
        $filter1->initFieldDefinition(); 
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
        $filter2 = new TestFilter("Testfield_name2","=","test2");  
        $filterGroup2 = new GenericStoreFilterGroup("or");
        
        $filterGroup->addSubFilter($filter1);
        $filterGroup->addSubFilter($filter2);
        $filterGroup2->addSubFilter($filter2);
        $filterGroup->addSubFilter($filterGroup2);
        
        $groupArray = ($filterGroup->__toArray());
       
        $this->assertEquals(
            count($groupArray["filters"]),
            3,
            "Not all filters were stored in the filter object"
        );
        $this->assertEquals(
            $groupArray["filters"][0],
            $filter1->__toArray(),
            "Wrong filter definition in group"
        );
        $this->assertEquals(
            $groupArray["filters"][1],
            $filter2->__toArray(),
            "Wrong filter definition in group"
        );
        $this->assertEquals(
            $groupArray["filters"][2],
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
    
}
