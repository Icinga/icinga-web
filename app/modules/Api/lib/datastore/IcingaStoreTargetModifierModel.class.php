<?php

class IcingaStoreTargetModifierModel extends IcingaBaseModel implements IDataStoreModifier
{
    protected $mappedParameters = array(
        "target" => "target", 
        "fields" => "fields"
    );
    
    protected $allowedFields = array();
    protected $defaultFields = array();
    protected $sortFields = array();
    protected $groupFields = array();
    protected $aliasDefs = array();
    
    private $target = array();
    private $fields = array();
    private $joins = array();
    
    
    public function handleArgument($name,$value) {
        switch($name) {
            case 'target':
                $this->setTarget($value); 
                break;
            case 'fields': 
                $this->setFields($value);
                break;
         }
    }
  
    public function getAllowedFields() {
        return $this->allowedFields;
    }

    public function getAliasDefs() {
        return $this->aliasDefs;
    }
  
    public function setTarget($target) {
        $this->target = $target;
    } 
    
    public function setFields($fields) {
        if(!is_array($fields))
            $fields = array($fields);
        $regExp = "/^(?<alias>\w+)\.(?<field>\w+)/";
        foreach($fields as $field) {
            // check for alias 
            $match = array();
            preg_match($regExp,$field,$match);
            if(isset($match["alias"])) {
                $this->addAlias($match["alias"]);
                $this->fields[] = $field;
            } else { 
                $this->fields[] = "my.".$field;
            }   
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
         
        $o->from($this->target." my");
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
    
    public function addAlias($alias) {
        if(!isset($this->aliasDefs[$alias]))
            throw new AppKitException("Tried to access hoststore field with invalid alias $alias");
    
        $join = $this->aliasDefs[$alias];
        $join["alias"] = $alias;
        if(in_array($join,$this->joins))
            return true; // already added      
        $this->joins[] = $join;
   }        
    
    public function __getJSDescriptor() {
        return array(
            "type" => "fields",
            "allowedFields" => $this->allowedFields,
            "defaultFields" => $this->defaultFields,
            "sortFields" => $this->sortFields,
            "groupFields" => $this->groupFields,
            "params" => $this->getMappedArguments()
        );
    }
}

?>
