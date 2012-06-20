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

/**
* Allows to import configurations in the same structure like the settings.xml
* Config settigns will be available through the AgaviConfig class
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de> 
**/
class AppKitSubSettingConfigHandler extends AgaviXmlConfigHandler
{
    const XML_NAMESPACE = 'http://agavi.org/agavi/config/parts/settings/1.0';
    
    public function execute(AgaviXmlConfigDomDocument $document)
    {
        $data = array();
        $prefix = "org.icinga.";
       
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'settings');
       
        foreach($document->getConfigurationElements() as $cfg) {
         
            foreach($cfg->get('settings') as $setting) {
                $localPrefix = $prefix;
                 
                // let's see if this buddy has a <settings> parent with valuable information
                if($setting->parentNode->localName == 'settings') {
                    if($setting->parentNode->hasAttribute('prefix')) {
                        $localPrefix = $setting->parentNode->getAttribute('prefix');
                    }
                }
                    
                $settingName = $localPrefix . $setting->getAttribute('name');

                if($setting->hasAgaviParameters()) {

                    $data[$settingName] = $setting->getAgaviParameters();
                } else {
                    $data[$settingName] = AgaviToolkit::literalize($setting->getValue());
                }
            }
          
        }
        $code = 'AgaviConfig::fromArray(' . var_export($data, true) . ');';
        return $this->generate($code, $document->documentURI);

    }
}
