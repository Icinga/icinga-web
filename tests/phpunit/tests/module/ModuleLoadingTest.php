<?php

class ModuleLoadingTest extends PHPUnit_Framework_TestCase {
    
    const MODULE_NAME = 'TestDummy';
    
    public function testModuleLoading() {        
        $ctx = IcingaWebTestTool::getContext();
        $ctx->getController()->initializeModule(self::MODULE_NAME);
        $re = AgaviConfig::get('modules.' . strtolower(self::MODULE_NAME) . '.enabled');
        $this->assertTrue($re);
    }

    public function testModuleRouting() {
        $ctx = IcingaWebTestTool::getContext();
        $this->assertEquals(1, preg_match('/\/modules\/testdummy\/test\/route\/c1$/', $ctx->getRouting()->gen('modules.testdummy.test1')));
    }
    
    public function testModuleDatabaseConfiguration() {
        $ctx = IcingaWebTestTool::getContext();
        
        $database = $ctx->getDatabaseManager()->getDatabase('icinga_test_database');
        
        IcingaWebTestTool::assertInstanceOf('AgaviPdoDatabase', $database);
        
        $resource = $database->getConnection();
        
        IcingaWebTestTool::assertInstanceOf('PDO', $resource);
        
        $sth = $resource->prepare('SELECT count(*) as cnt from nsm_user where user_name=\'root\'');
        
        $re = $sth->execute();
        
        $this->assertTrue($re);
        
        $row = $sth->fetch();
        
        $this->assertEquals(1, $row['cnt']);
        
        $this->assertTrue($sth->closeCursor());
    }
    
    public function testModuleDataFail() {
        
        $this->setExpectedException('AgaviDatabaseException');
        
        $ctx = IcingaWebTestTool::getContext();
        
        $database = $ctx->getDatabaseManager()->getDatabase('icinga_test_database_fail');
        
        $resource = $database->getConnection();
    }
}