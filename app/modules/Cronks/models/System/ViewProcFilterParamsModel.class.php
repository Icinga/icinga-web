<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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
 * Handling parameters for grids
 * @author mhein
 *
 */
class Cronks_System_ViewProcFilterParamsModel extends CronksBaseModel {
    

    /**
     * our params array
     * @var array
     */
    private $params_array = array();
    private $jsonFilter = NULL;
    private $filterParser = NULL;
    
    public function setParamsFromJson($json) {
        $this->filterParser = new IcingaDQLViewFilter();

        if (is_string($json)) {
            $json = json_decode($json, true);
        }
        if(!$this->filterParser->isValidJsonFilter($json)) {
            throw new AppKitException("invalid filter");
        }
        $this->jsonFilter = $json;
    }
    
    /**
     * Set the params as an array
     *
     * @param array $p
     * @return boolean
     */
    public function setParams(array $p) {
        $this->params_array = $p;
        return true;
    }

    /**
     * This apply all parameters to the worker to
     * modify IcingaAPI search filters
     *
     * @param CronkGridTemplateWorker $template
     * @return boolean
     */
    public function applyToWorker(CronkGridTemplateWorker &$template) {
        foreach($this->params_array as $pKey=>$pVal) {
            $this->applyLegacyFilter($pKey,$pVal,$template);
        }
        if($this->jsonFilter) {
            $dqlAndValues = $this->filterParser->getDQLFromFilterArray($this->jsonFilter,$template);
            if($dqlAndValues[0])
                $template->getDQLQueryObject()->andWhere($dqlAndValues[0],$dqlAndValues[1]);
        }
        

        return true;
    }
    
    private function applyLegacyFilter($pKey, $pVal, CronkGridTemplateWorker $template) {
        $m = array();

        if (preg_match('@^(.+)-value$@', $pKey, $m)) {

            // Fieldname (xml field name)
            $name = $m[1];

            // The value
            $val = $pVal;

            // Operator
            $op = array_key_exists($name. '-operator', $this->params_array)
                  ? $this->params_array[ $name. '-operator' ]
                  : null;

            // Add a template worker condition
            $template->setCondition($name, $val, $op);
        }
    }
    
}