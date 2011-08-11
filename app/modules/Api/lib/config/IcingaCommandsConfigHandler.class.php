<?php

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

            $currentCommand["definition"] = $definition->item(0)->nodeValue;
            $currentCommand["parameters"] = $this->extractParamsFromCommandNode($command);;
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
