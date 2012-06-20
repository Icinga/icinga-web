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

class AppKitValidatorArgumentExtractor {
    private $requestParams = array();
    static public $NsValidation = array("key"=>"val","ns"=>"http://agavi.org/agavi/config/parts/validators/1.0");
    static public $NsEnvelope   = array("key"=>"ae","ns"=>"http://agavi.org/agavi/config/global/envelope/1.0");
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