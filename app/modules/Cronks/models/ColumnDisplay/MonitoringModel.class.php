<?php

class Cronks_ColumnDisplay_MonitoringModel extends CronksBaseModel implements AgaviISingletonModel {
    public function serviceStatus($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        return (string)IcingaServiceStateInfo::Create($val)->getCurrentStateAsHtml();
    }
    
    public function hostStatus($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        return (string)IcingaHostStateInfo::Create($val)->getCurrentStateAsHtml();
    }
    
    public function icingaConstants($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        $type = $method_params->getParameter('type');
        $ref = new ReflectionClass('IcingaConstantResolver');
    
        if (($m = $ref->getMethod($type))) {
            if ($m->isPublic() && $m->isStatic()) {
                return  AgaviContext::getInstance()->getTranslationManager()->_($m->invoke(null, $val));
            }
        }
    
        return $val;
    }
    
    public function icingaDowntimeType($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        static $classes = array(
        1	=> 'icinga-icon-service',
        2	=> 'icinga-icon-host'
        );
    
        return sprintf('<div class="icon-16 %s"></div>', $classes[$val]);
    }
    
    /*
     * Removed:
     * icingaDowntimeRunning($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row)
     * 
     * Functionallity was moved to JS side
     */
}