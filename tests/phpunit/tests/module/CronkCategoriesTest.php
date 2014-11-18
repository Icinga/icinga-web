<?php

class CronkCategoriesTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @group CronkCategory
     */
    public function testCategoryModel() {
        $ctx = IcingaWebTestTool::getContext();
        
        $category_data = $ctx->getModel('Provider.CronkCategoryData', 'Cronks');
        $this->assertInstanceOf('Cronks_Provider_CronkCategoryDataModel', $category_data);
        
        $category_security = $ctx->getModel('Provider.CronkCategorySecurity', 'Cronks');
        $this->assertInstanceOf('Cronks_Provider_CronkCategorySecurityModel', $category_security);
    }
    
    /**
     * @group CronkCategory
     */
    public function testCategoryLoading() {
        $ctx = IcingaWebTestTool::getContext();
        IcingaWebTestTool::authenticateTestUser();
        $category_data = $ctx->getModel('Provider.CronkCategoryData', 'Cronks');
        
        $categories = $category_data->getCategories();
        
        // Should not throw anything
        $category_data->getCategory('data');
        $category_data->getCategory('core');
        
        $this->assertGreaterThan(0, count($categories));
    }
    
    /**
     * @group CronkCategory
     */
    public function testCategoryRecord() {
        $ctx = IcingaWebTestTool::getContext();
        IcingaWebTestTool::authenticateTestUser();
        $category_data = $ctx->getModel('Provider.CronkCategoryData', 'Cronks');
        
        $category = $category_data->getCategory('data');
        
        $this->assertInternalType('array', $category);
        $this->assertCount(8, $category);
        
        $this->assertArrayHasKey('catid', $category);
        $this->assertArrayHasKey('count_cronks', $category); // dynamic
        $this->assertArrayHasKey('permission_set', $category); // dynamic
        $this->assertArrayHasKey('position', $category);
        $this->assertArrayHasKey('system', $category);
        $this->assertArrayHasKey('title', $category);
        $this->assertArrayHasKey('visible', $category);
        $this->assertArrayHasKey('collapsed', $category);
        
        $this->assertEquals('data', $category['catid']);
        # ignore deprecated category for the following test
        if ($category['catid'] !== 'data') {
            $this->assertGreaterThan(0, $category['count_cronks']);
        }
        $this->assertEquals(false, $category['permission_set']);
        $this->assertGreaterThan(0, $category['position']);
        $this->assertEquals(true, $category['system']);
        $this->assertEquals('Data', $category['title']);
        $this->assertEquals(true, $category['visible']);
    }
    
    /**
     * @group CronkCategory
     */
    public function testHasCategory() {
        $ctx = IcingaWebTestTool::getContext();
        IcingaWebTestTool::authenticateTestUser();
        $category_data = $ctx->getModel('Provider.CronkCategoryData', 'Cronks');
        
        $this->assertTrue($category_data->hasCategory('core'));
        $this->assertTrue($category_data->hasCategory('data'));
        $this->assertTrue($category_data->hasCategory('to'));
        
        $this->assertFalse($category_data->hasCategory('ZZZ_DOES_NOT_EXIST_#'));
    }
    
    /**
     * @group CronkCategory
     * @expectedException AppKitModelException
     * @expectedExceptionMessage Insuffcent credentials
     */
    public function testCredentialAccessToModel() {
        $ctx = IcingaWebTestTool::getContext();
        IcingaWebTestTool::authenticateTestUser();
        
        // Root without credential
        $ctx->getUser()->removeCredential('icinga.cronk.category.admin');
        
        $category_security = $ctx->getModel('Provider.CronkCategorySecurity', 'Cronks');
        $category_security->setCategoryUid('data');
        $category_security->updateRoles(array(100,200));
    }
    
    /**
     * @group CronkCategory
     */
    public function testModuleNoAccessToCategory() {
        $ctx = IcingaWebTestTool::getContext();
        IcingaWebTestTool::authenticateTestUser();
        
        // Permissions for data category
        $ctx->getUser()->addCredential('icinga.cronk.category.admin');
        $category_security = $ctx->getModel('Provider.CronkCategorySecurity', 'Cronks');
        $category_security->setCategoryUid('data');
        $category_security->updateRoles(array(4)); // 4==guest role
        
        // Revoke Admin credential
        $ctx->getUser()->removeCredential('icinga.cronk.category.admin');
        $ctx->getModel('Provider.CronkCategoryData', 'Cronks')->refreshUser();
        
        $exception_found = false;
        
        try {
            $category_security->setCategoryUid('data'); // Refresh
            $cat = $category_security->getRoles();
        } catch(AppKitModelException $e) {
            if ($e->getMessage() === 'Category not found: data') {
                $exception_found = true;
            }
        }
        
        $this->assertTrue($exception_found);
        
        // Reset
        $ctx->getUser()->addCredential('icinga.cronk.category.admin');
        $ctx->getModel('Provider.CronkCategoryData', 'Cronks')->refreshUser();
        $category_security->setCategoryUid('data');
        $category_security->updateRoles(array());
        $category_security->setCategoryUid('data'); // Refresh
        $cat = $category_security->getCategory();
        
        $this->assertEquals('Data', $cat['title']);
        
        // Test without credential and category without principals
        $ctx->getUser()->removeCredential('icinga.cronk.category.admin');
        $ctx->getModel('Provider.CronkCategoryData', 'Cronks')->refreshUser();
        $category_security->setCategoryUid('data');
        $this->assertCount(0, $category_security->getRoles()); // GOT / no errors
        
        // Reset credential for following tests
        $ctx->getUser()->addCredential('icinga.cronk.category.admin');
    }
    
    /**
     * @group CronkCategory
     * @expectedException AppKitModelException
     * @expectedExceptionMessage Category not found: phpunit_test_123123
     */
    public function testCategoryCreation() {
        $ctx = IcingaWebTestTool::getContext();
        IcingaWebTestTool::authenticateTestUser();
        
        $catid = 'phpunit_test_123123';
        
        $catmodel = $ctx->getModel('Provider.CronkCategoryData', 'Cronks');
        
        $catmodel->createCategory(array(
            'catid' => $catid,
            'title' => 'PHPUNIT test category',
            'visible' => true,
            'position' => '123443'
        ));
        
        $test_cat = $catmodel->getCategory($catid);
        
        $this->assertEquals('123443', $test_cat['position']);
        
        $catmodel->deleteCategoryRecord($catid);
        
        // Throw at least
        $test_cat = $catmodel->getCategory($catid);
    }
    
    
}
