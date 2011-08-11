<?php
class AppKitValidatorArgumentExtractor {
    private $requestParams = array();
    static public $NsValidation = array("key"=>"val","ns"=>"http://agavi.org/agavi/config/parts/validators/1.0");
    static public $NsEnvelope	= array("key"=>"ae","ns"=>"http://agavi.org/agavi/config/global/envelope/1.0");
    private $currentDOM;


    public function execute(AgaviXmlConfigDomDocument $DOM) {
        $DOM->setDefaultNamespace(self::$NsValidation["ns"],self::$NsValidation["key"]);
        $xpath = $DOM->getXpath();
        $nsVal = self::$NsValidation["key"];
        $nsValURI = self::$NsValidation["ns"];
        $xpath->registerNamespace(self::$NsEnvelope["key"],self::$NsEnvelope["ns"]);
        $validators = $xpath->query("//".$nsVal.":validators");
        $params = array();
        foreach($validators as $val) {
            if ($val->getAttribute("method") && $val->getAttribute("method") != "write") {
                //only respect POST requests
                continue;
            }

            $validators = $xpath->query("//".$nsVal.":validator",$val);

            foreach($validators as $validator) {
                $params[] = $this->parseRequestParameter($validator);
            }
        }
        return $params;
    }

    private function parseRequestParameter(AgaviXmlConfigDomElement $validator) {
        $parameter = array();
        $parameter["required"]  = $validator->getAttribute("required");
        $parameter["type"]      = $validator->getAttribute("class");
        $validationParameters = null;
        $ae = self::$NsEnvelope["key"];
        foreach($validator->childNodes as $subEntry) {
            switch ($subEntry->nodeName) {
                case 'argument':
                    $parameter["argument"] = $subEntry->nodeValue;
                    break;

                case 'arguments':
                    $parameter["argument"] = array(
                                                 "base" => $subEntry->getAttribute("base"),
                                                 "arguments" => array()
                                             );
                    foreach($subEntry->childNodes as $argument) {
                        if (trim($argument->nodeValue)) {
                            $parameter["argument"]["arguments"][] = trim($argument->nodeValue);
                        }
                    }
                    break;

                case $ae.':parameter':
                case $ae.':parameters':
                    $validationParameters = $subEntry;
            }
        }

        if ($validationParameters) {
            $parameter["parameters"] = $validationParameters->getAgaviParameters();
        }

        return array_unique($parameter);
    }

}