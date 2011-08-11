<?php

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
