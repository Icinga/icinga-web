<?php
class TestDataStoreModel extends AbstractDataStoreModel 
{
    public $readCalled = false;
    public $insertCalled = false;
    public $deleteCalled = false;
    public $updateCalled = false;
    
    public function canDelete() {
        return "testCredential";   
    }
    public function canUpdate() {
        return array("not_you","testCredential");
    }

    public function execRead() {
        $this->readCalled = true;
    }
    public function execInsert($d) {
        $this->insertCalled = true;
    }
    public function execDelete($d) {
        $this->deleteCalled = true;
    }
    public function execUpdate($d) { 
        $this->updateCalled = true;
    }
}

class DataStoreTest extends PHPUnit_Framework_TestCase
{
    public function testAlwaysAllowedRead() {
        $model = new TestDataStoreModel();
        $model->doRead();
    
        $this->checkCalls(
            $model,
            array(true,false,false,false),
            "disallowed write"
        );         
    }
    
   

    public function testNotAllowedWrite() {
        $model = new TestDataStoreModel();
        $e = null;
        try {
            $model->doInsert("no data");
        } catch(DataStorePermissionException $p) {
            $e = $p;
        }
        IcingaWebTestTool::assertInstanceOf(
            "DataStorePermissionException",
            $e,
            "Wrong or no exception thrown on disallowed write"
        );
        $this->checkCalls(
            $model,
            array(false,false,false,false),
            "disallowed write"
        );    
    }   
    
    public function testUpdateCredentialPermission() {
        $model = new TestDataStoreModel();
        $e = null;
        try {
            $model->doUpdate("no data");
        } catch(DataStorePermissionException $p) {
            $e = $p;
        }
        IcingaWebTestTool::assertInstanceOf(
            "DataStorePermissionException",
            $e,
            "Wrong or no exception thrown on disallowed write"
        );
        $this->checkCalls(
            $model,
            array(false,false,false,false),
            "disallowed update"
        );
        $context = AgaviContext::getInstance();
		$context->getUser()->addCredential("testCredential");
        $model->doUpdate("no data");
        $this->checkCalls(
            $model,
            array(false,false,true,false),
            "allowed update"
        );
    }
    
    public function testDeleteCredentialPermission() {
        $model = new TestDataStoreModel();
        $e = null;   
        $context = AgaviContext::getInstance();
		$context->getUser()->addCredential("testCredential");
        $model->doDelete("no data");
        $this->checkCalls(
            $model,
            array(false,false,false,true),
            "delete"
        );
   } 

    private function checkCalls(TestDataStoreModel $model,array $rights,$method) {
        $this->assertEquals(
            $model->readCalled,
            $rights[0],
            "Data store read was ".($rights[0] ? 'not' : '')." executed on ".$method
        );
        $this->assertEquals(
            $model->insertCalled,
            $rights[1],
            "Data store write was ".($rights[1] ? 'not' : '')." executed on ".$method
        );
        $this->assertEquals(
            $model->updateCalled,
            $rights[2],
            "Data store update was ".($rights[2] ? 'not' : '') ." executed on ".$method
        );
        $this->assertEquals(
            $model->deleteCalled,
            $rights[3],
            "Data store delete was ".($rights[3] ? 'not' : '') ." executed on ".$method
        );
    }
}
