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


class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView {
    
    public function executeJavascript(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
	
            $content = '';    
            $this->copyConfigToJavascript($content);
            $content .= $this->getAttribute('content');
            
            $etag = $this->getAttribute("etag",rand());
            
            $this->getResponse()->setHttpHeader('Cache-Control', 'private', true);
            $this->getResponse()->setHttpHeader('Pragma', null, true);
            $this->getResponse()->setHttpHeader('Expires', null, true);
            $this->getResponse()->setHttpHeader('ETag', '"'. $etag. '"', true);

            if ($this->getAttribute('existsOnClient',false)) {
                $this->getResponse()->setHttpStatusCode("304");
                return "";
            }
            
            ob_start("ob_gzhandler");

            return $content;
        }
    }

    public function executeCss(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            $content = $this->getAttribute('content');

            return $content;
        }
    }
    
    /**
     * Mapping configuration items from AgaviConfig to JS AppKit.util.Config
     * @param string $content 
     */
    private function copyConfigToJavascript(&$content) {
        $map = AgaviConfig::get('modules.appkit.js_config_mapping', array ());
        if (count($map)) {
            $out = 'var Icinga = { AppKit: { configMap: {} }};'. chr(10);
            foreach ($map as $target=>$source) {
                $val = AgaviConfig::get($source ? $source : $target, null);
                $out .= 'Icinga.AppKit.configMap['. json_encode($target). '] = '. json_encode($val). ';'. chr(10);
            }
            $content .= $out;
        }
    }

}
