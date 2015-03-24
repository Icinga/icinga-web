<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class StoreFilterFieldApiValues {
    public $target = "";
    public $column = "";
    public $filter = "";
    public function __construct($target,$column,array $filter = array()) {
        $this->target = $target;
        $this->column = $column;
        $this->filter = $filter;
    }

    public function __toArray() {
        return array(
                   "target"=>$this->target,
                   "column"=>$this->column,
                   "filter"=>$this->filter
               );
    }
}

class StoreFilterField {
    public static $VALUE_ANY= "ANY";
    public static $VALUE_NUMERIC= "NUMERIC";

    public static $NUMERIC_MODIFIER = array(
                                          "is exact" => "=",
                                          "is smaller than" => "<",
                                          "is smaller/equal than" => "<=",
                                          "is bigger than" => ">",
                                          "is bigger/equal than" => ">=",
                                          "is not" => "!="
                                      );

    public static $TEXT_MODIFIER = array(
                                       "is exact" => "=",
                                       "is not" => "!=",
                                       "is like" => "LIKE",
                                       "is not like" => "NOT LIKE"
                                   );
    public $displayName;
    public $name;
    public $operators;
    public $possibleValues = array();

    public function __construct() {
        $this->operators = self::$TEXT_MODIFIER;
    }

    static public function init($display,$name,array $values = array()) {
        $o = new self();
        $o->displayName = $displayname;
        $o->name = $name;
        $o->possibleValues = $values;
    }

    public function __toArray() {
        $vals = $this->possibleValues;

        if (is_object($this->possibleValues)) {
            $vals = $this->possibleValues->__toArray();
        }

        return array(
                   "displayName" => $this->displayName,
                   "name" => $this->name,
                   "operators" => $this->operators,
                   "values" => $vals
               );
    }
}
