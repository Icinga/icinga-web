<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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


class IcingaCommandsConfigHandler extends AgaviXmlConfigHandler {
    private $document = null;
    private $xpath;
    private $cmd_parameters = array();
    const XML_NAMESPACE = 'http://icinga.org/api/config/parts/icingacommands/1.0';

    public function execute(AgaviXmlConfigDomDocument $document) {
        $this->document = $document;
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'ic');
        $this->setupXPath();

        $this->fetchParameters();
        $commands = $this->fetchCommands();
        return $this->generate('return '.var_export($commands,true));
    }

    private function setupXPath() {
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace("ic",self::XML_NAMESPACE);
        $this->xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
    }

    private function fetchCommands() {
        $xpath = $this->xpath;
        $document = $this->document;
        $commands = $xpath->query("//ae:configurations/ae:configuration/ic:commands/node()");
        $commandResult = array();
        foreach($commands as $command) {
            $currentCommand = array();

            if (!$command->hasAttributes()) {
                continue;
            }

            $definition = $xpath->query("ic:definition",$command);
            $isSimple = $xpath->query("ic:isSimple",$command);
            $type = $xpath->query("ic:type",$command);
            $iconCls = $xpath->query("ic:iconCls",$command);
            $label = $xpath->query("ic:label",$command);
            $currentCommand["definition"] = $definition->item(0)->nodeValue;
            
            if($isSimple->length>0)
                $currentCommand["isSimple"] = $isSimple->item(0)->nodeValue;
            else 
                $currentCommand["isSimple"] = false;
            
            $currentCommand['type'] = $type->item(0)->nodeValue;
            $currentCommand['iconCls'] = $iconCls->item(0)->nodeValue;
            $currentCommand['label'] = $label->item(0)->nodeValue;
            $currentCommand["parameters"] = $this->extractParamsFromCommandNode($command);
            $commandResult[$command->attributes->getNamedItem("name")->value] = $currentCommand;
        }
        return $commandResult;
    }

    private function extractParamsFromCommandNode(DOMNode $command) {
        $parameters = $this->xpath->query("ic:parameters/node()",$command);
        $params = array();
        foreach($parameters as $param) {

            if (!$param->hasAttributes()) {
                continue;
            }

            $curparams = $this->getAttributesAsArray($param);
            $ref = isset($curparams["ref"]) ? $curparams["ref"] : null;

            if ($ref && isset($this->cmd_parameters[$ref])) {
                $curparams = $this->cmd_parameters[$ref];
            }

            $params[$curparams["name"]] = $curparams;
        }
        return $params;
    }

    private function fetchParameters() {
        $document = $this->document;
        $xpath = $this->xpath;
        $nodes = $xpath->query("//ae:configurations/ae:configuration/ic:parameters/node()");
        $parameters = array();
        foreach($nodes as $node)    {
            if (!$node->hasAttributes()) {
                continue;
            }

            $parameters[$node->attributes->getNamedItem("name")->value] = $this->getAttributesAsArray($node);
        }
        $this->cmd_parameters = $parameters;
    }

    private function getAttributesAsArray(DOMNode $node) {
        $param = array();
        foreach($node->attributes as $attrName=>$attr) {
            $param[$attrName] = $attr->value;
        }

        return $param;

    }
}
