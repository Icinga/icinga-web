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

class ApiProviderMissingActionException extends AgaviConfigurationException {};
class ApiProviderMissingModuleException extends AgaviConfigurationException {};

class AppKitRoutingConfigHandler extends AgaviRoutingConfigHandler {

    const XML_NAMESPACE = 'http://icinga.org/appkit/config/parts/routing/1.0';

    private $apiProviders = array();

    /**
    * returns the module/action definition of Ext.direct exports in this routing
    * Mainly used for test-cases
    * @returns array    An assoc array with ("module"=>string, "action"=>string elements)
    *
    * @author   Jannis Mosshammer <jannis.mosshammer@netways.de>
    */
    public function getApiProviders() {
        return $this->apiProviders;
    }

    /**
     * @see     AgaviRoutingConfigHandler::execute()
     * @author  Marius Hein <marius.hein@netways.de>
     */
    public function execute(AgaviXmlConfigDomDocument $document) {
        // set up our default namespace
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'routing');
        return $this->parent_execute($document);
    }

    /**
    * parent::execute would overwrite the routing default namespace with the one agavi uses, so we
    * cannot simply call the parent one (self::XML_NAMESPACE wouldn't refer to http://icinga.org/...)
    * This is just a copy of @See AgaviRoutingConfigHandler::execute and additionally calls the Ext.direct
    * Provider
    *
    * @param    AgaviXmlConfigDomDocument   The DOMDocument to parse
    *
    * @author   Jannis Moßhammer   <jannis.mosshammer@netways.de>
    *
    **/
    private function parent_execute(AgaviXmlConfigDomDocument $document) {
        $routing = AgaviContext::getInstance($this->context)->getRouting();
        $this->unnamedRoutes = array();
        $routing->importRoutes(array());
        $data = array();

        foreach($document->getConfigurationElements() as $cfg) {
            if ($cfg->has('routes')) {
                $this->parseRoutesExtended($routing, $cfg->get('routes'));
                $this->parseApiProviders();
                $this->parseRoutes($routing,$cfg->get('routes'),$parent = null);
            }
        }

        return serialize($routing->exportRoutes());
    }

    /**
    *   Delegates route-parsing to the inherited AgaviXmlConfigHandler but extracts
    *   information about ext.direct routes
    *
    * @param      AgaviRouting The routing instance to create the routes in.
    * @param      mixed        The "roles" node (element or node list)
    * @param      string       The name of the parent route (if any).
    *
    * @author     Jannis Moßhammer <jannis.mosshammer@netways.de>
    */
    protected function parseRoutesExtended($routing,$routes,$parent = null) {
        foreach($routes as $route) {
            if ($route->hasAttribute("api_provider")) {
                $this->fetchApiProviderInformation($route);
            }

            if ($route->has('routes')) {
                $this->parseRoutesExtended($routing,$route->get('routes'),$route);
            }
        }
    }

    private function parseApiProviders() {

        if (empty($this->apiProviders)) {
            return;
        }

        $extdirectParser = new AppKitApiProviderParser();
        $extdirectParser->execute($this->apiProviders);
    }


    /**
    * Extracts module and action information from the current route
    * @param    AgaviDomElement The route elment to search for
    *
    * @author   Jannis Moßhammer <jannis.mosshammer@netways.de>
    * @throws   MissingModuleException  Indicates that a route without a module should be exported
    * @throws   MissingActionException  Indicates that a route without an action should be exported
    */
    protected function fetchApiProviderInformation(DomElement $route) {
        $module = AppKitXmlUtil::getInheritedAttribute($route, "module");
        $action = AppKitXmlUtil::getInheritedAttribute($route, "action");

        if (!$action) {
            $r = print_r($route->getAttributes(),1);
            throw new ApiProviderMissingActionException("Missing action in route exported for ApiProvider route settings: ".$r);
        }

        if (!$module) {
            $r = print_r($route->getAttributes(),1);
            throw new ApiProviderMissingModuleException("Missing module in route exported for ApiProvider route settings: ".$r);
        }

        if ($module != null && $action != null) {
            $toExport = array(
                            "module" => $module,
                            "action" => $action
                        );

            if (!in_array($toExport,$this->apiProviders)) {
                array_push($this->apiProviders,$toExport);
            }
        }
    }


}
