<?php
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
        $this->setupXPath();

        $this->fetchDQLViews();
        return $this->generate("return ".var_export($this->views,true));
    }

    private function setupXPath() {
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace("db","http://icinga.org/icinga/config/global/api/views/1.0");
        $this->xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
    }

    private function fetchDQLViews() {
        $dqlRoot = $this->xpath->query('//ae:configuration/node()');

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
            $vals = array_replace_recursive($base,$vals);
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
            "orderFields" => array()
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
                case 'orderBy':
                    $dqlView["orderFields"][] = $viewParam->nodeValue;
                    break;
                case 'credential':
                    $type = $viewParam->attributes->getNamedItem('type')->nodeValue;

                    $credential = array(
                        "name" => $viewParam->attributes->getNamedItem('name')->nodeValue,
                        "type" => $type
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

            }
        }

        $this->views[$dqlView["name"]] = $dqlView;
    }

}

?>
