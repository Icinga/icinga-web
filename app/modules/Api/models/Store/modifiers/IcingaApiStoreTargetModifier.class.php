<?php

class IcingaApiStoreTargetModifier implements IDataStoreModifier
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
                $this->target = $value 
                break;
            case 'fields': 
                $this->fields = $value;
                if(!is_array($this->fields))
                    $this->fields= array($this->fields);
                break;
            case 'join':
                $this->joins = $value;
        }
    }
    
    public function getMappedParameters() {
        return $this->mappedParameters;
    }

    public function modify(&$o) {
        $this->modifyImpl($o);
    }

    protected function modifyImpl(IcingaDoctrine_Query &$o) {
        $o->select(implode(",",$this->fields));
        $o->from($this->target);
        foreach($this->joins as $join) {
            if(isset($join["type"]) {
                if($join["type"] == "inner") {
                    $o->innerJoin($join["src"].".".$join["relation"]." ".$join["alias"]);
                    continue;
                }
            }
            $o->leftJoin($join["src"].".".$join["relation"]." ".$join["alias"]);
        }
    }
}
