<?php
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
