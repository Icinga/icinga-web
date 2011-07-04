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
    
    public function testModuleCronks() {
        
        IcingaWebTestTool::authenticateTestUser();
        
        $ctx = IcingaWebTestTool::getContext();
        
        $cronk_model = $ctx->getModel('Provider.CronksData', 'Cronks');
        
        $this->assertTrue($cronk_model->hasCronk('dummyTestCronk1'));
        
        $cronk = $cronk_model->getCronk('dummyTestCronk1');
        
     //   $this->assertInternalType('array', $cronk);
        
        $this->assertTrue($cronk['system']);
        
     //   $this->assertInternalType('array', $cronk['ae:parameter']);
        
        $this->assertEquals('dummyTestCronk1', $cronk['cronkid']);
        
        $this->assertArrayHasKey('module', $cronk);
        $this->assertArrayHasKey('action', $cronk);
        $this->assertArrayHasKey('hide', $cronk);
        $this->assertArrayHasKey('description', $cronk);
        $this->assertArrayHasKey('name', $cronk);
        $this->assertArrayHasKey('categories', $cronk);
        $this->assertArrayHasKey('image', $cronk);
        $this->assertArrayHasKey('ae:parameter', $cronk);
    }
    
    public function testModuleCategories() {
        
        IcingaWebTestTool::authenticateTestUser();
        
        $ctx = IcingaWebTestTool::getContext();
        
        $cronk_model = $ctx->getModel('Provider.CronksData', 'Cronks');
        
        $data = $cronk_model->combinedData();
        
   //     $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('cronks', $data);
        $this->assertArrayHasKey('categories', $data);
        
        $jarray = $data['cronks']['dummy_test_category1'];
        
        $this->assertEquals(3, count($jarray));
        
        $this->assertArrayHasKey('rows', $jarray);
        $this->assertArrayHasKey('success', $jarray);
        $this->assertArrayHasKey('total', $jarray);
        
    //    $this->assertInternalType('array', $jarray['rows']);
        $this->assertEquals(1, count($jarray['rows']));
        
    //    $this->assertInternalType('array', $data['categories']);
    }
    
    public function testCronkNotInGroup() {
        
        IcingaWebTestTool::authenticateTestUser();
        
        $ctx = IcingaWebTestTool::getContext();
        
        $cronk_model = $ctx->getModel('Provider.CronksData', 'Cronks');
        
        $cronks = $cronk_model->getCronks();
        
        $this->assertFalse(array_key_exists('dummyTestCronk3', $cronks));
    }
    
    public function testModuleTranslations() {
        $ctx = IcingaWebTestTool::getContext();
        $tm = $ctx->getTranslationManager();
        
        $tm->setLocale('en');
        
        $this->assertEquals('en', $tm->getCurrentLocaleIdentifier());
        
        $this->assertEquals('test1-trans', $tm->_('test1', 'testdummy.text_simple'));
        $this->assertEquals('test2-trans', $tm->_('test2', 'testdummy.text_simple'));
        
        $this->assertEquals(date('Y'), $tm->_d(date('Y-m-d H:i:s'), 'testdummy.date_year'));
        $this->assertEquals(date('m'), $tm->_d(date('Y-m-d H:i:s'), 'testdummy.date_month'));
    }
}
