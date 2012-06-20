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

class AppKitApiProviderParser  {
    private $jsResult;
    private $remotingParams = array();
    public function execute(array $directProviders) {


        foreach($directProviders as $provider) {
            $dom = $this->getValidatorXMLForAction($provider['module'],$provider['action']);
            $this->getRemotingParams($dom,$module,$action);
        }
    }

    private function parseDOMToExtDirect(AgaviXmlConfigDomDocument $dom,$module,$action) {
        if (!isset($this->remotingParams[$module])) {
            $this->remotingParams[$module] = array();
        }

        $this->remotingParams[$module][] = array(
            array(
                "name" => $action, 
                "params" => $this->getParametersFromDOM($dom)
            )
        );
    }

    private function getParametersFromDOM(AgaviXmlConfigDomDocument $DOM) {
        $xpath = new DOMXPath($DOM);

    }

    /**
    * Fetches the Validation xml for the action/module combination and returns it as
    * an DOMDocument
    *
    * @param    string  The module name
    * @param    string  The action to get the validation xml for
    * @return   AgaviXmlConfigDomDocument
    *
    * @author   Jannis Moßhammer<jannis.mosshammer@netways.de>
    * @throws   AgaviConfigurationException     when module or action does not exist
    */
    protected function getValidatorXMLForAction($module,$action) {
        // get Module path
        $path = AgaviToolkit::literalize('%core.module_dir%')."/".$module;

        if (!file_exists(AgaviToolkit::normalizePath($path))) {
            throw new AgaviConfigurationException("Couldn't find module ".$module);
        }

        // get Validation file
        $actionPath = str_replace(".","/",$action);
        $xml = $path."/validate/".$actionPath.".xml";

        if (!file_exists(AgaviToolkit::normalizePath($path))) {
            throw new AgaviConfigurationException("Couldn't find validation file for ".$action);
        }

        $dom = new AgaviXmlConfigDomDocument();
        $dom->load(AgaviToolKit::normalizePath($xml));

        //TODO: Validate xml
        return $dom;
    }

}
