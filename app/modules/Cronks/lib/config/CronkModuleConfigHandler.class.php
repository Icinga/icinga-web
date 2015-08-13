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


class CronkModuleConfigHandler extends AgaviXmlConfigHandler {

    const XML_NAMESPACE = 'http://icinga.org/cronks/config/parts/cronks/1.0';

    public function execute(AgaviXmlConfigDomDocument $document) {
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'cronk');

        $config = $document->documentURI;

        $cronks = array();
        $categories = array();

        foreach($document->getConfigurationElements() as $cfg) {
            $this->getXmlConfiguration($cfg, 'cronks', 'cronk', $cronks);
            $this->getXmlConfiguration($cfg, 'categories', 'category', $categories);
        }

        $code  = 'return '. var_export(array($cronks, $categories), true);

        return $this->generate($code, $config);
    }

    private function getXmlConfiguration(AgaviXmlConfigDomElement $cfg, $tuple_element, $item_element, &$store) {
        if ($cfg->has($tuple_element)) {
            foreach($cfg->get($tuple_element) as $item) {
                if ($item->hasAgaviParameters()) {
                    $store[$item->getAttribute('name')] = $item->getAgaviParameters();
                }
            }
            return true;
        }
    }

}

?>
