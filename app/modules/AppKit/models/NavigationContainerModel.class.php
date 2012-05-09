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
 * Checking menu entries for specific conditions (e.g. credentials)
 * @author jmosshammer
 *
 */
class AppKit_NavigationContainerModel extends AppKitBaseModel {
    /**
     * @var AgaviWebRouting
     */
    private $ro = null;
    private $config = null;
    private $user;

    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->user = $context->getUser();
    }

    /**
     * Returns the menu tree as json
     * @return string
     */
    public function getJsonData() {

        return json_encode($this->createMenuDescriptor());
    }

    public function readJsonFromConfig() {
        $config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.config_dir%/menu.xml'));
        $menu = $this->getAllowedMenuPoints($config);

        return $menu;
    }

    private function getAllowedMenuPoints(array $config) {
        $m = array();
        foreach($config as $elem) {
            if (isset($elem["credential"])) {
                $credentials = explode(";",$elem["credential"]);

                if (!$this->hasCredentials($credentials)) {
                    continue;
                }
            }

            if (isset($elem['items'])) {
                $items = $this->getAllowedMenuPoints($elem['items']);
                $elem['items'] = $items;
            }

            $m[] = $elem;
        }
        return $m;
    }
    private function hasCredentials(array $credentials) {
        foreach($credentials as $credential) {
            if (!$this->user->hasCredential($credential)) {
                return false;
            }
        }
        return true;
    }

    private function createMenuDescriptor() {
        $cfg = $this->readJsonFromConfig();


        return $cfg;
    }


}
