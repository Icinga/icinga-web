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


class Cronks_System_CronkPortalSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {

        $customViewFields  = array(
                                 "cr_base"=>false,
                                 "sortField"=>false,
                                 "sortDir"=>false,
                                 "groupField"=>false,
                                 "groupDir"=>false,
                                 "template"=>false,
                                 "crname"=>false,
                                 "filter"=>false,
                                 "title"=>false
                             );
        $requiredViewFields = array("template", "crname", "title");

        $rd->setParameter("isURLView",true);
        foreach($customViewFields as $name=>$val) {
            $val = $rd->getParameter($name,null);

            if ($val == null) {
                if (in_array($name, $requiredViewFields)) {
                    $rd->setParameter("isURLView",false);
                    break;
                }
                else {
                    unset($customViewFields[$name]);
                }
            }
            else {
                $customViewFields[$name] = $val;
            }
        }

        if ($rd->getParameter("isURLView"))  {
            if (isset($customViewFields["cr_base"]) and trim($customViewFields["cr_base"]) !== "") {
                $this->formatFields($customViewFields);
            }
            $rd->setParameter("URLData",json_encode($customViewFields));
        }

        $this->setupHtml($rd);
        $this->setAttribute('_title', 'Icinga.Cronks.CronkPortal');
    }

    /**
     * Converts the url and agavi routing friendly format of the parameters to
     * its original values
     */
    public function formatFields(array &$fields) {
        $formatFields = array("cr_base");
        foreach($formatFields as $fieldName) {
            $field = $fields[$fieldName];
            $result = array();

            // Because of empty arrays in javascript
            $field = preg_replace('/;$/', '', $field);

            // split at ;
            $fieldParts = explode(';',$field);

            foreach($fieldParts as $currentField) {
                if (!$currentField) {
                    continue;
                }

                //rebuild field
                $parts = array();

                if (preg_match("/(\w*?)\|(.*?)_\d+=(.*)/",$currentField,$parts)) {

                    // @todo: Works better without, quickfix!
                    //if(!isset($result[$parts[1]]))
                    //  $result[$parts[1]] = array();

                    $result[$parts[1]."[".$parts[2]."]"] = $parts[3];
                } else {
                    $str = explode("=",$currentField);

                    $result[$str[0]] = $str[1];
                }
            }
            $fields[$fieldName] = $result;
        }

    }
}

?>
