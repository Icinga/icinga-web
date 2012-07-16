<?php

class Bug2857Test extends PHPUnit_Framework_TestCase {
    
    const HOSTGROUP = 'XXX_DOES_NOT_EXIST';
    const TEMPLATE = 'icinga-open-problems-template';
    const CONNECTION = 'icinga';
    
    protected $target_value = null;
    protected $principal_target = null;
    
    public function setUp() {
        IcingaWebTestTool::authenticateTestUser();
        $ctx = IcingaWebTestTool::getContext();
        $user = $ctx->getUser();
        $record = $user->getNsmUser();
        
        $principal = $record->principal;
        
        $target = Doctrine::getTable('NsmTarget')
            ->findOneBy("target_name",'IcingaHostgroup');
        
        $this->principal_target = new NsmPrincipalTarget();
        $this->principal_target->NsmPrincipal = $principal;
        $this->principal_target->NsmTarget = $target;
        
        $this->target_value = new NsmTargetValue();
        $this->target_value->tv_key = 'hostgroup';
        $this->target_value->tv_val = self::HOSTGROUP;
        
        $this->principal_target->NsmTargetValue[] = $this->target_value;
        
        $this->target_value->save();
        
        $this->principal_target->save();
        
        $this->assertGreaterThan(0, $this->target_value->tv_pt_id);
        $this->assertGreaterThan(0, $this->principal_target->pt_id);
    }
    
    public function tearDown() {
        $this->assertInstanceOf("NsmTargetValue", $this->target_value);
        $this->assertInstanceOf("NsmPrincipalTarget", $this->principal_target);
        $this->target_value->delete();
        $this->principal_target->delete();
    }
    
    /**
     * @group Bug
     */
    public function testBug() {
        $ctx = IcingaWebTestTool::getContext();
        
        $file = AppKitFileUtil::getAlternateFilename(
            AgaviConfig::get('modules.cronks.xml.path.grid'), 
            self::TEMPLATE, 
            '.xml'
        );
        
        $template = new CronkGridTemplateXmlParser(
            $file->getRealPath(),
            $ctx
        );
        
        $template->parseTemplate();
        
        $worker = CronkGridTemplateWorkerFactory
            ::createWorker($template, $ctx, self::CONNECTION);
        
        $this->assertGreaterThanOrEqual(0, $worker->countResults());
    }
}