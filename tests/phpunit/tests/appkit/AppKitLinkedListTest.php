<?php


class AppKitLinkedListTests extends PHPUnit_Framework_TestCase {
    /**
    * @group AppKit
    */
    public function testCreateInstance() {
        $llist = new AppKitLinkedList();
        $this->assertTrue($llist != null);
    }
    
    /**
     * @depends testCreateInstance
     * @group AppKit
     */
    public function testPush() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $llist = new AppKitLinkedList();
        $llist->push($i);
        $this->assertEquals($i->value,$llist->current(),"Current didn't return pushed element");
        $llist->push($i2);
        $this->assertEquals($i->value,$llist->current(),"Current didn't return shifted element");
        $this->assertEquals($i->next,$i2, "Next pointer not correctly chained");
        $this->assertEquals($i2->previous,$i, "Previous pointer not correctly chained");
    }
    
    /**
     * @depends testPush
     * @group AppKit
     */
    public function testCount() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $llist = new AppKitLinkedList();
        $this->assertEquals($llist->count(),0,"Wrong count result");
        $llist->push($i);    
        $this->assertEquals($llist->count(),1,"Wrong count result");
        $llist->push($i2);
        $this->assertEquals($llist->count(),2,"Wrong count result");
    
    }
    
    /**
     * @depends testPush
     * @group AppKit
     */
    public function testToString() {
        $data = array(
            "id1" => "test",
            "id2" => "test2",
            "id3" => "test3",
            "id4" => "test4",
        );
        $llist = new AppKitLinkedList();
        
        $str = "START=>";
        foreach($data as $id=>$elem) {
            $llist->push(new AppKitLinkedListItem($elem,$id));
            $str .= "{".$id."=".$elem."}=>";
        }
        $str .= "END";
        $this->assertEquals($llist->toString(),$str,"toString failed");      
    }
    
    /**
     * @depends testCreateInstance
     * @group AppKit
     */
    public function testUnshift() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $llist = new AppKitLinkedList();
        $llist->unshift($i);
        $this->assertEquals($i->value,$llist->current(),"Current didn't return shifted element");
        $llist->unshift($i2);
        $this->assertEquals($i->value,$llist->current(),"Current didn't return shifted element");
        $this->assertEquals($i2->next,$i, "Next pointer not correctly chained");
        $this->assertEquals($i->previous,$i2, "Previous pointer not correctly chained");
        $this->assertEquals($llist->count(),2,"Wrong count result");
    }
    
    /**
     * @depends testPush
     * @depends testUnshift
     * @group AppKit 
     */
    public function testNext() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2"); 
        $llist = new AppKitLinkedList();
        $llist->next(); // shouldn't have any effect
        $llist->push($i);
        $this->assertEquals($i->value,$llist->current(),"Current didn't return shifted element");
        $llist->push($i2);
        $this->assertEquals($i->value,$llist->current(),"Current didn't return shifted element");
        $llist->next();
        $this->assertEquals($i2->value,$llist->current(),"Current didn't return second element after next element ");
        $llist->next();
        $this->assertEquals($llist->current(),null,"Out of bounds wasn't detected"); 
    }

    /**
     * @depends testNext
     * @group AppKit
     */
    public function testPrev() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $llist = new AppKitLinkedList();
        $llist->prev(); // shouldn't have any effect
        
        $llist->push($i); 
        $llist->push($i2); 
        $llist->next();
        $llist->next(); // at end
    
        $llist->prev();
        $this->assertEquals($i2->value,$llist->current(),"Current didn't return second element after prev");
        $llist->prev();
        $this->assertEquals($i->value,$llist->current(),"Current didn't return first element after second prev");
        $llist->prev();
        $this->assertEquals($llist->current(),null,"Out of bounds wasn't detected"); 
    }

    /**
     * @depends testNext
     * @group AppKit
     */
    public function testPop() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $llist = new AppKitLinkedList();
        $llist->push($i);
        $llist->push($i2);  
        $this->assertEquals($llist->count(),2,"Wrong count result");
        $llist->next();
        $this->assertEquals($i2->value,$llist->current(),"Current didn't return pushed element");
        $this->assertEquals($i2->value,$llist->pop(),"pop() didn't return last element");
        $this->assertEquals($llist->count(),1,"Wrong count result");
        $this->assertEquals($i->value,$llist->current(),"Current didn't return first element after pop()");  
        $this->assertEquals($i->value,$llist->pop(),"Second pop() didn't return last element");
        $this->assertEquals($llist->count(),0,"Wrong count result");
        $this->assertEquals($llist->current(),null,"List should have been empty"); 
    }
    
    /**
     * @depends testNext
     * @group AppKit
     */
    public function testShift() { 
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $llist = new AppKitLinkedList();
        $llist->push($i);
        $llist->push($i2);  
        $this->assertEquals($llist->count(),2,"Wrong count result");
        
        $this->assertEquals($i->value,$llist->shift(), "Shift() didn't return first element");
        $this->assertEquals($i2->value,$llist->current(),"Current didn't return second element after shift() ".$llist->toString());  
        $this->assertEquals($llist->count(),1,"Wrong count result");

        $this->assertEquals($i2->value,$llist->shift(), "Second shift() didn't return first element");
        $this->assertEquals($llist->current(),null,"List should have been empty"); 

        $this->assertEquals($llist->count(),0,"Wrong count result");
    }

    /**
     * @depends testShift
     * @group AppKit
     */
    public function testTop() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $i3 = new AppKitLinkedListItem("test3");
        $llist = new AppKitLinkedList();
        $llist->push($i);
        $llist->push($i2);  
        $llist->push($i3);
        $llist->next();
        $this->assertEquals($i->value,$llist->top(), "Top didn't return first item");
        $llist->shift(); 
        $this->assertEquals($i2->value,$llist->top(), "Second top didn't return first item"); 
        $llist->shift(); 
        $this->assertEquals($i3->value,$llist->top(), "Third top didn't return first item"); 
        $llist->shift(); 
        $this->assertEquals(null,$llist->top(), "top() on empty list didn't return null");
    }

    /**
     * @depends testPop
     * @group AppKit
     */
    public function testBottom() {
        $i = new AppKitLinkedListItem("test");
        $i2 = new AppKitLinkedListItem("test2");
        $i3 = new AppKitLinkedListItem("test3");
        $llist = new AppKitLinkedList();
        $llist->push($i);
        $llist->push($i2);  
        $llist->push($i3);
        $llist->next();
        $this->assertEquals($i3->value,$llist->bottom(), "bottom() didn't return last item");
        $llist->pop(); 
        $this->assertEquals($i2->value,$llist->bottom(), "Second bottom didn't return last item"); 
        $llist->pop(); 
        $this->assertEquals($i->value,$llist->bottom(), "Third bottom didn't return last item"); 
        $llist->pop(); 
        $this->assertEquals(null,$llist->bottom(), "bottom() on empty list didn't return null");
    }
    
    /**
     * @depends testPush
     * @depends testUnshift
     * @group AppKit
     */
    public function testOffsetExistsNumeric() {
        $llist = new AppKitLinkedList();
        $llist->push("test2");
        $llist->push("test3");
        $llist->unshift("test1");
        $this->assertFalse($llist->offsetExists(-2),"Negative offset not recognized");
        $this->assertTrue($llist->offsetExists(2),"Offset 2 not found in 3 element list");
        $this->assertFalse($llist->offsetExists(3),"Out of bounds offset not recognized in 3 element list"); 
    }
    
    /**
     * @depends testPush
     * @depends testUnshift
     * @group AppKit
     */
    public function testOffsetExistsId() {
        $llist = new AppKitLinkedList();
        $llist->push(array("id"=>"testId","value"=>"test2"));
        $llist->push(new AppKitLinkedListItem("test3","testId2"));
        $llist->unshift("test1"); 
        
        $this->assertTrue($llist->offsetExists("testId"),"Array id offset  not found in list");
        $this->assertTrue($llist->offsetExists("testId2"),"LinkedListObject id offset not found in list");
        $this->assertFalse($llist->offsetExists("flop"),"Out of bounds offset not recognized in 3 element list"); 
    }
 
    /**
     * @depends testOffsetExistsNumeric
     * @group AppKit
     */
    public function testOffsetGetNumeric() {
        $llist = new AppKitLinkedList();
        $llist->push("test2");
        $llist->push("test3");
        $llist->unshift("test1");
        $this->assertEquals($llist->offsetGet(-5),null,"Negative offset not recognized");
        $this->assertEquals($llist->offsetGet(2),"test3","Offset 2 not found in 3 element list");
        $this->assertEquals($llist->offsetExists(3),null,"Out of bounds offset not recognized in 3 element list"); 
    }
    
    /**
     * @depends testOffsetExistsId
     * @group AppKit
     */
    public function testOffsetGetId() {
        $llist = new AppKitLinkedList();
        $llist->push(array("id"=>"testId","value"=>"test2"));
        $llist->push(new AppKitLinkedListItem("test3","testId2"));
        $llist->unshift("test1"); 
        
        $this->assertEquals($llist->offsetGet("testId"),array("id"=>"testId","value"=>"test2"),"Array id offset  not found in list");
        $this->assertEquals($llist->offsetGet("testId2"),"test3","LinkedListObject id offset not found in list");
        $this->assertEquals($llist->offsetGet("flop"),null,"Not existing id returned list element"); 
    }
    
    /**
     * @depends testOffsetGetId
     * @group AppKit
     */
    public function testOffsetSet() {
        $llist = new AppKitLinkedList();
        $llist->push(new AppKitLinkedListItem("test2","testId"));
        $llist->push(new AppKitLinkedListItem("test3","testId2"));
        $replacer = new AppKitLinkedListItem("foo","testId");
        $llist->unshift("test1"); 
        $this->assertEquals($llist->offsetGet("testId"),"test2","Array id offset  not found in list");
        $llist->offsetSet("testId",$replacer); 
        $this->assertEquals($llist->offsetGet("testId"),"foo","Replacement failed");
    }

    /**
     *
     * @depends testOffsetSet
     * @group AppKit
     */
    public function testOffsetPush() {
        $llist = new AppKitLinkedList();
        $llist->push(new AppKitLinkedListItem("test2","testId"));
        $llist->push(new AppKitLinkedListItem("test3","testId2"));
        $replacer = new AppKitLinkedListItem("foo","testId");
        $llist->offsetPush("testId",$replacer);
        $this->assertEquals($llist->offsetGet(1),"foo","Added element not found");
    }

    /**
     * @depends testOffsetSet
     * @group AppKit
     */
    public function testOffsetUnshift() {
        $llist = new AppKitLinkedList();
        $llist->push(new AppKitLinkedListItem("test2","testId"));
        $llist->push(new AppKitLinkedListItem("test3","testId2"));
        $replacer = new AppKitLinkedListItem("foo","testId");
        $llist->offsetUnshift("testId",$replacer);
        $this->assertEquals($llist->offsetGet(0),"foo","Added element not found");
    }
}
