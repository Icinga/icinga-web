<?php

class InvalidFilterTypeException extends AppKitException {};

class GenericStoreFilterGroup extends StoreFilterBase implements Iterator 
{
    protected $type = "AND"; 
    protected $subFilters = array();

    protected $possibleTypes = array(
        "and" => "AND", 
        "or"=>"OR"
    );

    public function getType() {
        return $this->type;
    }
    public function setType($type) {
        if(isset($this->possibleTypes[$type])) {
            $this->type = $this->possibleTypes[$type];
        } else if(in_array($type,$this->getPossibleTypes())) {
            $this->type = $type;
        } else {
            throw new InvalidFilterTypeException("Type ".$type." is not supported"); 
        }
    }
    
    public function addSubFilter(StoreFilterBase $b) {
        $this->subFilters[] = $b;
    }
    public function getSubFilters() {
        return $this->subFilters;
    }
    public function getPossibleTypes() {
        return $this->possibleTypes;
    }
    public function __getJSDescriptor() {
        return array(
            "type" => "group",
            "types" => $this->possibleTypes  
        );
    }

    /**
    * Try to parse from an (array) of values
    * @param    array   Filter parameters to parse
    * @param    DataStoreFilterModifier Backreference to parser \
    *                                   (in order to be able to parse the subqueries)
    * @return   GenericStoreFilterGroup on success, otherwise null
    **/
    public static function parse($filter,$parser, $instance=null) {
        $o;
        if($instance)
            $o = new $instance();
        else
            $o = new self();
        if(!isset($filter["type"]) || !isset($filter["items"]))
            return null;
        if(!in_array($filter["type"],$o->getPossibleTypes()) || empty($filter["items"]))
            return null;
        $o->setType($filter["type"]);
        $availableItems = array();
        foreach($filter["items"] as $item) {
            $subFilter = $parser->tryParse($item);
            
            if(is_a($subFilter,'StoreFilterBase'))
                $o->addSubFilter($subFilter); 
         }
       
        return $o;
    }
    
    public function __construct($type = null, array $subFilters = array()) {
        if($type)
            $this->setType($type);
        foreach($subFilters as $subFilter) {
            $this->addSubFilter($subFilters);
        }
    }
    
    public function __toArray() {
        $group = array("type"=>$this->getType(),"filters"=>array());
        foreach($this as $filter) {
            $group["items"][] = $filter->__toArray();
        }
        return $group;
    }   
    
    public function __toString() {
        return json_encode($this->__toArray());
    }

    // Iterator implementation
    public function current() {
        return current($this->subFilters); 
    }
    public function next() {
        next($this->subFilters);
    }
    public function rewind() {
        reset($this->subFilters);
    }
    public function valid() {
        return (current($this->subFilters) !== false);
    }
    
    /**
    * key() always returns the type of the filtergroup
    *
    * @return String    The type of this filtergroup 
    **/ 
    public function key() {
        return $this->type;
    }
     
}
