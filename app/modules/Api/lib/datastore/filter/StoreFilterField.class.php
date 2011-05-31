<?php

class StoreFilterFieldApiValues 
{
    public $action = "";
    public $module = "";
    public $filter = ""; 
}

class StoreFilterField 
{  
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
}
