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


abstract class JasperConfigBaseModel extends IcingaBaseModel {

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->applyJasperConfigToParameters($this->getParameter('jasperconfig'));
    }

    /**
     * Fill up the parameter array with keys from the jasper configuration made in the
     * module.xml.
     * @param string $config_ns
     * @throws AppKitModelException
     */
    private function applyJasperConfigToParameters($config_ns) {
        $config = AgaviConfig::get($config_ns);

        if ($config === null) {
            throw new AppKitModelException('Jasperconfiguration "'. $config_ns. '" not in agavi config space');
        }

        if (!is_array($config)) {
            throw new AppKitModelException('Jasperconfig must be an array!');
        }

        if (!array_key_exists('jasper_url', $config)) {
            throw new AppKitModelException('Jasperconfig needs "jasper_url" as parameter');
        }

        $this->setParameters($config);
    }

    /**
     * Test security related resources against our jasper tree root
     * to prevent data accessing without permission.
     * @param string $uri
     * @return boolean pass the security check
     */
    protected function checkUri($uri) {
        return (boolean)preg_match('/^'. preg_quote($this->getParameter('tree_root'), '/'). '/', $uri);
    }
}
?>
