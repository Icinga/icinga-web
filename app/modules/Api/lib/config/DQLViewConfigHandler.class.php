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

/**
 * Config parser for te sources.xml files, which define DQL queries that can
 * be used in tempaltes
 *
 * @author jmosshammer
 */
class DQLViewConfigHandler extends AgaviXmlConfigHandler {
    private $xpath = null;
    private $document;
    private $views = array();
    public function execute(AgaviXmlConfigDomDocument $document) {
        $this->document = $document;

        $this->fetchDQLViews($this->setupXPath($document));
        $this->importModuleConfigurations();
        return $this->generate("return ".var_export($this->views,true));
    }

    private function importModuleXML($accessLocation) {
        $document = new AgaviXmlConfigDomDocument();
        $document->load(AgaviToolkit::expandDirectives($accessLocation));

        $this->fetchDQLViews($this->setupXPath($document));
    }


    private function importModuleConfigurations() {
        $moduleDir = AgaviToolkit::literalize("%core.module_dir%");
        $modules = scandir($moduleDir);
        foreach($modules as $folder) {
            $dir = $moduleDir;
            if($folder == ".." || $folder == "." || $folder == "Api")
                continue;
            $dir = $dir."/".$folder."/";

            if(!is_dir($dir) || !is_readable($dir))
                continue;
            $accessLocation = $dir."config/views.xml";
            if(file_exists($accessLocation) && is_readable($accessLocation))
                $this->importModuleXML($accessLocation);

        }
    }

    private function setupXPath($document) {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace("db","http://icinga.org/icinga/config/global/api/views/1.0");
        $xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
        return $xpath;
    }

    private function fetchDQLViews(DOMXPath $xpath) {
        $dqlRoot = $xpath->query('//ae:configuration/node()');

        foreach($dqlRoot as $node) {

            if($node->nodeType != XML_ELEMENT_NODE)
                continue;
            $this->parseDQLPart($node);
        }
        foreach($this->views as $name=>&$vals) {
            if(!$vals["base"]) {
                continue;
            }
            if(!isset($this->views[$vals["base"]])) {
                AppKitLogger::warn("View %s references to unknown base query %s",$name,$vals["base"]);
                continue;
            }
            $base = $this->views[$vals["base"]];
            $vals = AppKitArrayUtil::replaceRecursive($base, $vals);
        }

    }


    private function parseSubSetting(array &$setting,$viewParam,$type) {
        switch($type) {
            case "auto":
                $setting["alias"] =  $viewParam->attributes->getNamedItem('startAlias')->nodeValue;
                break;
            case "custom":
                $setting["dql"] = $viewParam->nodeValue;
                break;
            case "dql":
                $setting["calls"] = array();
                foreach($viewParam->childNodes as $childNode) {
                    if($childNode->nodeType != XML_ELEMENT_NODE)
                        continue;
                    $setting["calls"][] = array(
                        "type" => $childNode->nodeName,
                        "arg" => $childNode->nodeValue
                    );
                }
                break;
            default:
                $setting["params"] = array();
                foreach($viewParam->childNodes as $childNode) {
                    if($childNode->nodeType != XML_ELEMENT_NODE)
                        continue;
                    if($childNode->nodeName == "parameter") {
                        $paramName = $childNode->attributes->getNamedItem('name')->nodeValue;
                        $setting["params"][$paramName] = $childNode->nodeValue;
                    }
                }
                break;
        }

    }

    private function parseDQLPart(DOMNode $node) {

        $dqlView = array(
            "name" => $node->attributes->getNamedItem('name')->nodeValue,
            "credentials" => array(),
            "connection" => false,
            "filter" => array(),
            "merge" => array(),
            "base" => false,
            "extend" => array(),
            "orderFields" => array(),
            "customvariables" => array()
        );
        if($node->attributes->getNamedItem('base')) {
            $dqlView["base"] = $node->attributes->getNamedItem('base')->nodeValue;
        }
        foreach($node->childNodes as $viewParam) {
            if($viewParam->nodeType != XML_ELEMENT_NODE)
                continue;

            switch($viewParam->nodeName) {
                case 'query':
                    $dqlView["baseQuery"] = $viewParam->nodeValue;
                    if($viewParam->attributes->getNamedItem('connection')) {
                        $dqlView["connection"] = $viewParam->attributes->getNamedItem('connection')->nodeValue;
                    }

                    break;
                case 'extend':
                    $extension = array("calls" => array());
                    foreach($viewParam->childNodes as $childNode) {
                        if($childNode->nodeType != XML_ELEMENT_NODE)
                            continue;
                        $extension["calls"][] = array(
                            "type" => $childNode->nodeName,
                            "arg" => $childNode->nodeValue
                        );
                    }
                    $dqlView["extend"][] = $extension;

                    break;
                case 'orderBy':
                    $dqlView["orderFields"][] = $viewParam->nodeValue;
                    break;
                case 'credential':
                    $type = $viewParam->attributes->getNamedItem('type')->nodeValue;
                    if($viewParam->attributes->getNamedItem('affects'))
                        $affects = $viewParam->attributes->getNamedItem('affects')->nodeValue;
                    else
                        $affects = null;

                    $credential = array(
                        "name" => $viewParam->attributes->getNamedItem('name')->nodeValue,
                        "type" => $type,
                        "affects" => $affects
                    );
                    $this->parseSubSetting($credential,$viewParam,$type);

                    $dqlView["credentials"][] = $credential;
                    break;
                case 'merge':
                    $mergeDefinition = array();
                    foreach($viewParam->childNodes as $mergeParam) {
                        if($mergeParam->nodeType != XML_ELEMENT_NODE)
                            continue;
                        if(!isset($mergeDefinition[$mergeParam->nodeName])) {
                            $mergeDefinition[$mergeParam->nodeName] = $mergeParam->nodeValue;
                        } else if(!is_array($mergeDefinition[$mergeParam->nodeName])) {
                            $mergeDefinition[$mergeParam->nodeName] = array($mergeDefinition[$mergeParam->nodeName],$mergeParam->nodeValue);
                        } else {
                            $mergeDefinition[$mergeParam->nodeName][] = $mergeParam->nodeValue;
                        }
                    }
                    $dqlView["merge"][] = $mergeDefinition;
                    break;
                case 'filter':
                    $type = $viewParam->attributes->getNamedItem('type')->nodeValue;

                    $filter = array(
                        "name" => $viewParam->attributes->getNamedItem('name')->nodeValue,
                        "type" => $type
                    );
                    $this->parseSubSetting($filter,$viewParam,$type);

                    $dqlView["filter"][$filter["name"]] = $filter;
                    break;

                case 'customvariables':
                    $customvariable = array();
                    $this->parseSubSetting($customvariable, $viewParam, 'customvariable');
                    $dqlView['customvariables'][] = $customvariable;
                    break;
            }
        }

        $this->views[$dqlView["name"]] = $dqlView;
    }

}

?>
