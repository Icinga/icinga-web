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


class Api_ApiCommandInfoSuccessView extends IcingaApiBaseView {

    private $commands = array ();

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
        $this->commands = $container->getAttribute('commands', null, array ());
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'ApiCommandInfo');
    }

    public function executeXml(AgaviRequestDataHolder $rd) {

        $dom = new DOMDocument('1.0', 'utf-8');
        $root = $dom->createElement('results');
        $dom->appendChild($root);
        $this->xml2Array($this->commands, $root, $dom);

        return $dom->saveXML();
    }

    private function xml2Array(array $array, DOMElement $root, DOMDocument $dom) {
        foreach ($array as $key=>$value) {
            if (is_array($value)) {
                $sub = $dom->createElement($key);
                $this->xml2Array($value, $sub, $dom);
                $root->appendChild($sub);
            } else {
                $root->appendChild($dom->createElement($key, $value));

            }
        }
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        if ($rd->getParameter('extjs')) {
            $json = new AppKitExtJsonDocument();
            $json->hasField('definition');
            $json->hasField('type');
            $json->hasField('isSimple');
            $json->hasField('iconCls');
            $json->hasField('label');
            $json->setData($this->commands);
            $json->setSuccess(true);
            return $json->getJson();
        } else {
            return json_encode(array(
                'success' => true,
                'results' => $this->commands
            ));
        }
    }
}
