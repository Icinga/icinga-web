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

class IcingaDQLViewFilterElement {
    private $field = NULL;
    private $operator = NULL;
    private $value = NULL;
    private $negateOffset = 0;
    
    public function __construct(array $json,$negate = false, CronkGridTemplateWorker $tpl = null) {
        $this->field = $tpl->getTemplateFilterField($json["field"]);
        $this->field = $tpl->getView()->enableFilter($this->field);
        $this->field = $tpl->getView()->getAliasedTableFromDQL($this->field);

        $this->operator = $json["operator"];
        $this->value = $json["value"] ? $json["value"] : '0' ;
        $this->negateOffset = $negate ? 1 : 0;
    }
    
    public function hasValue() {
        return $this->value != NULL;
    }
    
    public function getValue() {
        if($this->hasValue())
            return $this->value;
    }
    
    public function toDQL() {
        $op = IcingaDQLViewFilter::$OPERATOR_TYPES[$this->operator];
        $this->value = sprintf($op[2],$this->value);
        return sprintf("%s %s ?", $this->field, $op[$this->negateOffset]);
    }
}

class IcingaDQLViewFilterGroup {
    private $values = array();
    private $type = "and";
    private $negate = false;
    private $template = Null;
    
    public function __construct(array $json,$type,$parentNegated = false,CronkGridTemplateWorker $tpl = null) {
        $this->template = $tpl;
        $this->negate = $parentNegated;
        if(strtolower($type) == "not") {
            $this->type = "and";
            $this->negate = !$this->negate;
        } else {
            $this->type = $type;
        }
        $this->children = $json;
    }

    public function toDQL() {
        $dql = "(";
        $els = array();
        if(empty($this->children))
            return false;
        foreach($this->children as $child) {
            if(IcingaDQLViewFilter::isGroup($child)) {
                $keys = array_keys($child);
                $subEl = new IcingaDQLViewFilterGroup(
                    $child[$keys[0]],$keys[0],
                    $this->negate,$this->template
                );
                $els[] = $subEl->toDQL();
                $this->values = array_merge($this->values, $subEl->getValues());
            } else {
                $subEl = new IcingaDQLViewFilterElement($child,$this->negate,$this->template);
                $els[] = $subEl->toDQL();
                $this->values[] = $subEl->getValue();
            }    
        }
        $cleared = array();
        foreach($els as $element) {
            if($element == "")
                continue;
            $cleared[] = $element;
        }
        if(empty($cleared)) {
            return false;
        }
        return "(".join(" ".$this->type." ",$cleared).")";
    }
    
    public function getValues() {
        return $this->values;
    }
}



class IcingaDQLViewFilter {
    public static $CONJUNCTION_TYPES = array("and","or","not");
    public static $OPERATOR_TYPES = array(
        "="         => array("=","!=","%s"),
        ">"         => array(">","<=","%s"),
        "<"         => array("<",">=","%s"),
        "contain"   => array("LIKE","NOT LIKE","%%%s%%"),
        "is"        => array("=","!=","%s")
    );

    public static function isGroup(array $el) {
        $key = array_keys($el);
        $key = $key[0];
        if(!in_array(strtolower($key),self::$CONJUNCTION_TYPES)) {
            return false;
        }
        if(!is_array($el[$key])) {
            return false;
        }
        return true;
    }
    
    public function getDQLFromFilterArray(array $jsonArray,$template) {
        $keys = array_keys($jsonArray);
        $group = new IcingaDQLViewFilterGroup($jsonArray[$keys[0]],$keys[0],false,$template);
        $dql = $group->toDQL();

        return array($dql,$group->getValues());
    }
    
    public function isValidFilterElement($jsonEl) {
        $required = array("label","value","operator","field");
        $valid = true;
        foreach($required as $type) {
            $valid = $valid && isset($jsonEl[$type]);
            if(!$valid)
                AppKitLogger::warn("Missing element: $type (%s)",$jsonEl);
        }
        if(!$valid) {
            return false;
        }
        $valid = $valid && isset(self::$OPERATOR_TYPES[$jsonEl["operator"]]);
        if(!$valid)
            AppKitLogger::warn("invalid element %s ", $jsonEl);
        return $valid;
    }
    
    public function isValidJsonFilter(array $json) {
        
        foreach($json as $name => $jsonEl) {
            if(!is_numeric($name)) {
                if(!$this->isValidJsonFilter($jsonEl))
                    return false;
                return true;
            }
            if($this->isGroup($jsonEl)) {
                $key = array_keys($jsonEl);
                $key = $key[0];   
                if(!$this->isValidJsonFilter($jsonEl[$key]))
                    return false;
                
            } else if(!$this->isValidFilterElement($jsonEl)) {
                return false;
            }
        }
        return true;
    }
    
    
}