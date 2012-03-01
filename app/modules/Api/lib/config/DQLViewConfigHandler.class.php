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

    }
    
    private function parseDQLPart(DOMNode $node) {
        
        $dqlView = array(
            "name" => $node->attributes->getNamedItem('name')->nodeValue,
            "credentials" => array(),
            "merge" => array(),
            "orderFields" => array()
        );
        
        foreach($node->childNodes as $viewParam) {
            if($viewParam->nodeType != XML_ELEMENT_NODE)
                continue;
            
            switch($viewParam->nodeName) {
                case 'raw':
                    $dqlView["baseQuery"] = $viewParam->nodeValue;
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
                    switch($type) {
                        case "auto":
                            $credential["alias"] =  $viewParam->attributes->getNamedItem('startAlias')->nodeValue;
                            break;
                        case "custom":
                            $credential["dql"] = $viewParam->nodeValue;
                            break;
                        case "dql":
                            $credential["calls"] = array();
                            foreach($viewParam->childNodes as $childNode) {
                                if($childNode->nodeType != XML_ELEMENT_NODE)
                                    continue;
                                $credential["calls"][] = array(
                                    "type" => $childNode->nodeName,
                                    "arg" => $childNode->nodeValue
                                );
                            }
                            break;
                        default:

                            $credential["params"] = array();
                            foreach($viewParam->childNodes as $childNode) {
                                if($childNode->nodeType != XML_ELEMENT_NODE)
                                    continue;
                                if($childNode->nodeName == "parameter") {
                                    $paramName = $childNode->attributes->getNamedItem('name')->nodeValue;
                                    $credential["params"][$paramName] = $childNode->nodeValue;
                                }
                            }
                            break;

                    }
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
            }
        }

        $this->views[$dqlView["name"]] = $dqlView;
    }

}

?>
