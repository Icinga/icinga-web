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
 * Resource files container
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
class AppKit_ResourcesModel extends AppKitBaseModel implements AgaviISingletonModel {

    /**
     * Collected resources
     * @var array string type => array files
     */
    protected $resources = array();

    /**
     * Collected agavi actions which produces javascript
     */
    protected $jactions = array();

    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $ctx, array $parameters = array()) {
        parent::initialize($ctx, $parameters);

        include AgaviConfigCache::checkConfig(
            AgaviConfig::get('core.config_dir') . '/javascript.xml'
        );
        include AgaviConfigCache::checkConfig(
            AgaviConfig::get('core.config_dir') . '/css.xml'
        );
    }

    /**
     * Get javascript files
     *
     * @return array javascript files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function getJavascriptFiles() {
        return $this->resources['javascript'];
    }

    /**
     * Get css files
     *
     * @return array css files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function getCssFiles() {
        return $this->resources['css'];
    }

    /**
    * Get css imports
    *
    * @return array css files
    *
    * @author Eric Lippmann <eric.lippmann@netways.de>
    * @since 1.5.0
    */
    public function getCssImports() {
        return $this->resources['css_import'];
    }

    public function getJavascriptActions() {
        return $this->jactions;
    }

}
