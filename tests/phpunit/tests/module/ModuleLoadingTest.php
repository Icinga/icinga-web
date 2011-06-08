<?php

class ModuleLoadingTest extends PHPUnit_Framework_TestCase {
    
    const MODULE_NAME = 'TestDummy';
    
    public function testModuleLoading() {        
        $ctx = IcingaWebTestTool::getContext();
        $ctx->getController()->initializeModule(self::MODULE_NAME);
        $re = AgaviConfig::get('modules.' . strtolower(self::MODULE_NAME) . '.enabled');
        $this->assertTrue($re);
    }

    /**
     * @depends testModuleLoading
     */
    public function testModuleRouting() {
        $ctx = IcingaWebTestTool::getContext();
        $this->assertEquals(1, preg_match('/\/modules\/testdummy\/test\/route\/c1$/', $ctx->getRouting()->gen('modules.testdummy.test1')));
    }
    
}