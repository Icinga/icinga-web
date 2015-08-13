<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
        1   => 'icinga-icon-service',
        2   => 'icinga-icon-host'
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
