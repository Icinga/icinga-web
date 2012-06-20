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

class UnknownJsonFieldValidatorTypeException extends AppKitException {};
/**
* AppKitJsonValidator
* Validates JSON input against another json descriptor defined in parameter
* 'format'.
* Fields can have 'ANY' (field must exist), 'ARRAY' (field must be array), /regular expression/ or
* nested validators.
*
* Example usage:
* <validator class="AppKitJsonValidator">
*   <argument>test</argument>
*   <ae:parameters>
*      <!-- export name for jsonfied output (as array) -->
*      <ae:parameter name="export">json_test</ae:parameter>
*      <ae:parameter name="format"><![CDATA[
*           {
*               "name": "ANY",
*               "year": /[1-2]\d{3}/,
*               "author" : {
*                   "name" : "ANY",
*                   "references": "ARRAY"
*               }
*           }
*      ]]></ae:parameter>
*      <ae:parameter name="invalid_format">The input had inconsistent format</ae:parameter>
*      <ae:parameter name="invalid_json">Invalid json provided</ae:parameter>
*   </ae:parameters>
* </validator>
*/
class AppKitJsonValidator extends AgaviValidator {
    public function validate() {
        $argument = $this->getArgument();
        $json =  $this->getData($argument);
        $data = json_decode($json,true);

        if (!is_array($data)) {
            $this->throwError("invalid_json");
            return false;
        }

        $format = $this->getParameter("format");

        if ($format) {
            // json workaround for slashes ins string
            $format = str_replace('\\','\\\\',$format);

            $decoded = json_decode($format,true);

            if (!is_array($decoded)) {
                $this->throwError("invalid_json");
                return false;
            }

            if (!$this->checkFormat($decoded,$data)) {
                return false;
            }
        }

        $this->export($data);
        foreach($data as $field=>$val) {
            $this->export($field,$val);
        }
        return true;
    }

    public function checkFormat(array $decoded,array $data) {

        foreach($decoded as $field=>$check) {

            if (!isset($data[$field])) {
                $this->throwError("invalid_format",$field);
                return false;
            }

            switch ($this->getCheckType($check)) {
                case 'ANY':
                    break;

                case 'ARRAY':
                    if (!is_array($data[$field])) {
                        return false;
                    }

                    foreach($data[$field] as $entry) {
                        if (is_array($entry)) {
                            if (!$this->checkFormat($check,$entry)) {
                                return false;
                            }
                        } else if (!preg_match("/".$check."/",$entry)) {
                            return false;
                        }
                    }

                    break;

                case 'SUBFORMAT':
                    if (!is_array($data[$field])) {
                        $this->throwError("invalid_format",$field);
                        return false;
                    }

                    return $this->checkFormat($check,$data[$field]);

                case 'REGEXP':

                    if (!preg_match($check,$data[$field])) {
                        $this->throwError("invalid_format",$field);
                        return false;
                    }

                    break;

                default:
                    throw new UnknownJsonFieldValidatorTypeException(
                        "JSON value type ".$check." is unknown");
            }
        }
        return true;
    }

    public function getCheckType(&$type) {

        if (is_array($type)) {
            if (count(array_diff_key(array_values($type), $type))) {
                return "SUBFORMAT";
            }

            $type = $type[0];
            return "ARRAY";
        }

        if ($type == "ANY") {
            return "ANY";
        }

        if (preg_match("/^\/.*\/$/",$type)) {
            return "REGEXP";
        }

        return false;
    }
}
