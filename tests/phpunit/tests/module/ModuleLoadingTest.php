<?php

class ModuleLoadingTest extends PHPUnit_Framework_TestCase {
    
    const MODULE_NAME = 'TestDummy';

    /**
     * @group Module 
     */
    public function testModuleLoading() {        
        $ctx = IcingaWebTestTool::getContext();
        $ctx->getController()->initializeModule(self::MODULE_NAME);
        $re = AgaviConfig::get('modules.' . strtolower(self::MODULE_NAME) . '.enabled');
        $this->assertTrue($re);
    }
    
	/**
     * @group Module 
     */
    public function testModuleRouting() {
        $ctx = IcingaWebTestTool::getContext();
        $this->assertEquals(1, preg_match('/\/modules\/testdummy\/test\/route\/c1$/', $ctx->getRouting()->gen('modules.testdummy.test1')));
    }
    
    /**
     * @group Module
     */
//     public function testModuleDatabaseConfiguration() {
//         $ctx = IcingaWebTestTool::getContext();
        
//         $database = $ctx->getDatabaseManager()->getDatabase('icinga_test_database');
        
//         IcingaWebTestTool::assertInstanceOf('AgaviPdoDatabase', $database);
        
//         $resource = $database->getConnection();
        
//         IcingaWebTestTool::assertInstanceOf('PDO', $resource);
        
//         $sth = $resource->prepare('SELECT count(*) as cnt from nsm_user where user_name=\'root\'');
        
//         $re = $sth->execute();
        
//         $this->assertTrue($re);
        
//         $row = $sth->fetch();
        
//         $this->assertEquals(1, $row['cnt']);
        
//         $this->assertTrue($sth->closeCursor());
//     }
    
    /**
     * @group Module
     */
    public function testModuleDataFail() {
        
        $this->setExpectedException('AgaviDatabaseException');
        
        $ctx = IcingaWebTestTool::getContext();
        
        $database = $ctx->getDatabaseManager()->getDatabase('icinga_test_database_fail');
        
        $resource = $database->getConnection();
    }
    
    /**
     * @group Module
     */
    public function testCronkSecurityModel() {
        
        // Name of a system cronk
        static $cronk_name = 'gridInstanceStatus';
        
        // Test init
        IcingaWebTestTool::authenticateTestUser();

        // Drop cache
        unset($_SESSION["icinga.cronks.cache.xml"]);
        
        // Initialize
        $ctx = IcingaWebTestTool::getContext();
        $cronks = $ctx->getModel('Provider.CronksData', 'Cronks');
        $security = $ctx->getModel('Provider.CronksSecurity', 'Cronks');

        // 1. Reset, test for empty groups
        $security->setCronkUid($cronk_name);
        $cronk = $security->getCronk();
        
        $this->assertNull($cronk['groupsonly']);
        
        // 2. Reset, Adding roles
        $security->updateRoles(array(1,3));
        $security->setCronkUid($cronk_name);
        $roles = $security->getRoles();
        
        $this->assertCount(2, $roles);
        
        // Test role names
        $this->assertEquals('appkit_admin', $roles[0]['role_name']);
        $this->assertEquals('icinga_user', $roles[1]['role_name']);
        
        $this->assertContains('appkit_admin', $security->getRoleNames());
        $this->assertContains('icinga_user', $security->getRoleNames());

        // Test role_ids
        $this->assertContains(1, $security->getRoleUids());
        $this->assertContains(3, $security->getRoleUids());
        
        // Test principal_ids
        $this->assertContains(3, $security->getPrincipals());
        $this->assertContains(4, $security->getPrincipals());

        // 3. Reset, remove roles and test again
        $security->updateRoles(array());
        $security->setCronkUid($cronk_name);
        $roles = $security->getRoles();
        
        $this->assertNull($roles);
    }
    
    /**
     * @group Module
     */
    public function testModuleCronks() {
        
        IcingaWebTestTool::authenticateTestUser();
        
        $ctx = IcingaWebTestTool::getContext();

        // Drop cache
        unset($_SESSION["icinga.cronks.cache.xml"]);

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
    
    /**
     * @group Module
     */
    public function testModuleCategories() {
        
        IcingaWebTestTool::authenticateTestUser();

        // Drop cache
        unset($_SESSION["icinga.cronks.cache.xml"]);
        
        $ctx = IcingaWebTestTool::getContext();
        
        $ctx->getUser()->removeCredential('icinga.cronk.admin');
        
        $cronk_model = $ctx->getModel('Provider.CronksData', 'Cronks', array(
            'lazy' => true
        ));
        
        $data = $cronk_model->combinedData();
        
        $this->assertArrayHasKey('cronks', $data);
        $this->assertArrayHasKey('categories', $data);
        
        $jarray = $data['cronks']['dummy_test_category1'];
        
        $this->assertEquals(3, count($jarray));
        
        $this->assertArrayHasKey('rows', $jarray);
        $this->assertArrayHasKey('success', $jarray);
        $this->assertArrayHasKey('total', $jarray);

        $this->assertEquals(1, count($jarray['rows']));
        
    //    $this->assertInternalType('array', $data['categories']);
    }
    
    /**
     * @group Module
     */
    public function testCronkNotInGroup() {
        
        IcingaWebTestTool::authenticateTestUser();
        
        $ctx = IcingaWebTestTool::getContext();
        
        $ctx->getUser()->removeCredential('icinga.cronk.admin');

        // Drop cache
        unset($_SESSION["icinga.cronks.cache.xml"]);
        
        $cronk_model = $ctx->getModel('Provider.CronksData', 'Cronks');
        
        $cronks = $cronk_model->getCronks();
        
        $this->assertFalse(array_key_exists('dummyTestCronk3', $cronks));
    }

     /**
      * @group Module
      */
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
