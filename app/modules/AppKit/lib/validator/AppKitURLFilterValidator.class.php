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

/**
 * Validator that creates an assoc array from URL Filter params which can be used for
 * further filter processing.
 * The result will be in the format:
 * Input: AND(NOTIFICATION|=|true;HOST_ID|>|3];OR(field|METHOD|value))
 * Export:
 *  array("type"=>"AND", field=>array(
 *      array("type" => "atom", field=>NOTIFICATION, method="=", value="true"
 *      array("type" => "atom", field=>HOSTS...,
 *      array("type" => "OR", field=>array
 *      (
 *          ...
 *      )
 *
 *  @author jmosshammer <jannis.mosshammer@netways.de>
 */

class AppKitURLFilterValidator extends AgaviValidator {
    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/validator/AgaviValidator#validate()
     */
    public function validate() {
        $filterArray = array();
        $argument = $this->getArgument();
        $urlData = $this->getData($argument);

        if (!$this->checkSyntax($urlData)) {
            return false;
        }

        if (!$filterArray = $this->createFilterGroups($urlData)) {
            return false;
        }

        $this->export($filterArray);
        return true;
    }

    /**
     * Checks whether the request fulfills the minimum syntax requirements.
     * It's not a perfect check, just some simple rules.
     *
     * @param string $data The request parameter
     */
    public function checkSyntax($data) {
        // Every open brace must have a close counterpart
        if (substr_count($data, '(') != substr_count($data, ')')
            || substr_count($data, '[') != substr_count($data, ']')) {
            $this->throwError("error_braces");
            return false;
        }

        /*
                // check general errors
                $check_regs = array(
                    "/(AND|OR)[^\(]/" // invalid filtergroup syntax
                );

                foreach($check_regs as $currentcheck) {
                    if(preg_match($currentcheck,$data)) {
                        $this->throwError("general_syntax");
                        return false;
                    }
                }
            */
        return true;
    }

    /**
     * Creates the filter groups and splits up the filter in its atomic filters
     * @param string $urlData
     */
    protected function createFilterGroups($urlData,array &$filterArray = null) {
        $curLevel = $urlData;

        if (!(substr($urlData,2) == 'OR' || substr($urlData,3) == 'AND')) {
            $urlData = "AND(".$urlData.")";
        }

        $nextLevel = array();
        $matches = preg_match_all("/(.*?)\((.*)\)/",$curLevel,$nextLevel);

        if (count($nextLevel) != 3) {
            $this->throwError("general_syntax");
            return false;
        }

        $field = array();
        // top level
        $this->processLevel($field,$nextLevel[2][0]);

        if (is_null($filterArray)) {
            $filterArray = array("type" => $nextLevel[1][0], "field" => $field);
        } else {
            $filterArray[] = array("type" => $nextLevel[1][0], "field" => $field);
        }


        return $filterArray;
    }


    protected function processLevel(&$target,$curLevel) {
        $fields = array();
        $fields=preg_split("/((?:AND|OR)\(.*?\){1,})/",$curLevel,-1,PREG_SPLIT_DELIM_CAPTURE);
        $parts = array();
        foreach($fields as $field) {
            if (preg_match("/(\w*?)\((.*)\)/",$field)) {
                $this->createFilterGroups($field,$target);
            } else {
                $this->createFilter($target,$field);
            }
        }
    }

    protected function createFilter(&$target,$fields) {
        $fields = explode(";",$fields);
        foreach($fields as $field) {
            if (!$field) {
                continue;
            }

            preg_match_all("/(\w*?)\|(.*?)\|(.*)/",$field,$parts);

            if (count($parts) != 4) {
                continue;
            }

            $target[] = array(
                            "type"=>"atom",
                            "field"=>$parts[1],
                            "method"=>$parts[2],
                            "value"=>$parts[3]
                        );
        }
    }
}