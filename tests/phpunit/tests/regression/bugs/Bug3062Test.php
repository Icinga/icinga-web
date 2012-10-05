<?php

/**
 * Test case for https://dev.icinga.org/issues/3062
 */
class Bug3062Test extends PHPUnit_Framework_TestCase {
    
    public function testBug() {
        static $cronk_name = 'Bug3062Test_Cronk_Record';
        $ctx = IcingaWebTestTool::getContext();
        
        $model = $ctx->getModel('Provider.CronksData', 'Cronks');
        
        $testCronkData = array(
            'cid'           => $cronk_name,
            'name'          => $cronk_name,
            'description'   => $cronk_name,
            'categories'    => 'my',
            'ae:parameter' => array(
                'entity1'   => 'd=1&d=2&d=3',
                'entity2'   => '<a></a>',
                'entity3'   => '"a", \'b\''
            )
        );
        
        $record = $model->createCronkRecord($testCronkData);
        
        $this->assertInstanceOf('Cronk', $record);
        $this->assertEquals($cronk_name, $record->cronk_uid);
        $this->assertEquals($cronk_name, $record->cronk_name);
    }
    
}