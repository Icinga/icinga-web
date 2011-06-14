<?php

class Api_Store_Modifiers_StoreTargetModifierModel extends IcingaBaseModel implements IDataStoreModifier
{
    protected $mappedParameters = array(
        "target" => true,
        "joins" => false,
        "fields" => false
    );

    private $target = array();
    private $fields = array();
    private $joins = array();

    public function handleArgument($name,$value) {
        switch($name) {
            case 'target':
                $this->target = $value; 
                break;
            case 'fields': 
                $this->fields = $value;
                if(!is_array($this->fields))
                    $this->fields= array($this->fields);
                break;
            case 'joins':
                // check structure
                if(!is_array($value))
                    throw new InvalidArgumentException("Joins in storetarget must be defined as".
                        " an array");
                foreach($value as $val) {
                    if(!is_array($val))
                        throw new InvalidArgumentException("Joins must be defined as an array of json objects/arrays");
                    if(!isset($val["src"]) || !isset($val["relation"]) || !isset($val["alias"]))
                        throw new InvalidArgumentException("Invalid join: Must contain "+
                            "src, relation and alias (optional : type)");
                }
                $this->joins = $value;
        }
    }
    
    public function getMappedArguments() {
        return $this->mappedParameters;
    }

    public function modify(&$o) {
        // type safe call
        $this->modifyImpl($o);
    }

    protected function modifyImpl(Doctrine_Query &$o) { 
        $o->select(implode(",",$this->fields));
         
        $o->from($this->target);
        foreach($this->joins as $join) {
            
            if(isset($join["type"])) {
                if($join["type"] == "inner") { 
                    $o->innerJoin($join["src"].".".$join["relation"]." ".$join["alias"]);
                    continue;
                }
            }
            $o->leftJoin($join["src"].".".$join["relation"]." ".$join["alias"]);
        }
    }
}

?>
