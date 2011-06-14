<?php
class ApiStoreFilter extends GenericStoreFilter {
    /**
    * returns the filter definitions. These are mainly needed by the api-creation mechanismn
    * - invalid field/value combinations should be handled by the validator
    **/
    static private function getFilterDefs() {
        return array(
            array(
                "display_name"=>"Host name",
                "field"=>"display_name",
                "operators"=>StoreFilterField::$TEXT_MODIFIER,
                "possibleValues" => new StoreFilterFieldApiValues("ApiSearch","Api",array(
                    "target"=>"hosts",
                    "columns"=>"display_name"))
            )
            ,array(
                "display_name"=>"Service name",
                "field"=>"display_name",
                "operators"=>StoreFilterField::$TEXT_MODIFIER,
                "possibleValues" => new StoreFilterFieldApiValues("ApiSearch","Api",array(
                    "target"=>"services",
                    "columns"=>"display_name"))
            )
        );
        
    }

    public static function parse($filter,$parser) {
        if(!isset($filter["field"]) || !isset($filter["operator"]) || !isset($filter["value"]))
            return null;
        else return new self($filter['field'],$filter['operator'],$filter['value']);
    }

    public function initFieldDefinition() {
        $filters = self::getFilterDefs();
        foreach($filters as $filter) {
            $f = new StoreFilterField();
            $f->displayName = $filter["display_name"];
            $f->name = $filter["field"];
            $f->operators = $filter["operators"];
            $f->possibleValues = $filter["possibleValues"];
            $this->addFilterField($f); 
        } 
    }
    public function checkIfValid() {
        return true; //as the filter def is growing large validity should be checked by the validator
    } 
    private function formatValues() {
        $value = array();
        if(is_array($this->value)) {
            foreach($this->value as $v) {
                if(!is_numeric($v)) {
                    $v = "'".str_replace("'","\'",$v)."'";
                }
                $value[] = $v;
            }
            return $value;
        } else { 
            return "'".str_replace("'","\'",$this->value)."'";
        }
    }  

    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly) {
        $dql = $this->field." ".$this->operator;
        $v = $this->formatValues();
        if(is_array($v)) 
            $dql .= " (".implode(",",$v).")";
        else { 
            $dql .= " ".$v;
        }
        if($dqlOnly)
            return $dql;
        else 
            $q->where($dql);
        return;
    } 
} 

class ApiStoreFilterGroup extends GenericStoreFilterGroup {
    public static function parse($filter,$field,$instance = null) {
        return GenericStoreFilterGroup::parse($filter,$field,"ApiStoreFilterGroup");
    }
    
    public function __toDQL(IcingaDoctrine_Query $q) { 
        $dql = " (";
        $first = true;
        foreach($this->getSubFilters() as $filter) {
            $filterDQL = $filter->__toDQL($q,true);
            if($filterDQL)
                $dql .= (($first) ? ' ' : ' '.$this->getType())." ".$filterDQL;
            $first = false;
        }
        $dql .= ") ";
        if($this->getType() == 'OR')
            $q->orWhere($dql);
        else 
            $q->andWhere($dql);
    }
}

class Api_Store_Modifiers_StoreFilterModifierModel extends DataStoreFilterModifier implements IDataStoreModifier
{
   
    // Use these filterclasses
    protected $filterClasses = array(
        "ApiStoreFilter",
        "ApiStoreFilterGroup"
    );
 
    public function modify(&$o) {
        // type safe call
        $this->modifyImpl($o);
    }

    protected function modifyImpl(IcingaDoctrine_Query &$o) { 
        $f = $this->filter;
        if($f) {
            $f->__toDQL($o); 
        } 
    }
    
        
}  
