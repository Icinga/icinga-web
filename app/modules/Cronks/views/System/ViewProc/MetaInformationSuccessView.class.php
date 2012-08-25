<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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


class Cronks_System_ViewProc_MetaInformationSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Templates.MetaInformation');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        try {
            $modules = AgaviConfig::get("org.icinga.modules",array());
            $fileName = $rd->getParameter('template');
            $file = null;
            foreach($modules as $name=>$path) {
                if(file_exists($path."/config/templates/".$fileName.'.xml')) {
                    $file = AppKitFileUtil::getAlternateFilename($path."/config/templates/",$fileName, '.xml');
                }
            }

            if($file === null)
                $file = AppKitFileUtil::getAlternateFilename(AgaviConfig::get('modules.cronks.xml.path.grid'), $rd->getParameter('template'), '.xml');
            $template = new CronkGridTemplateXmlParser($file);
            $template->parseTemplate();
            $user = $this->getContext()->getUser()->getNsmUser();
            $data = $template->getTemplateData();
           
            if($user->hasTarget('IcingaCommandRestrictions')) {
                $template->removeRestrictedCommands();
            }
            
            return json_encode(array(
                                   'template'   => $template->getTemplateData(),
                                   'fields' => $template->getFields(),
                                   'keys'       => $template->getFieldKeys(),
                                   'params' => $rd->getParameters(),
                                   'connections' => IcingaDoctrineDatabase::$icingaConnections
                               ));
        } catch (AppKitFileUtilException $e) {
            $msg = 'Could not find template for '. $rd->getParameter('template');
            AppKitAgaviUtil::log('Could not find template for '. $rd->getParameter('template'), AgaviLogger::ERROR);
            return $msg;
        }
    }
    
}

?>