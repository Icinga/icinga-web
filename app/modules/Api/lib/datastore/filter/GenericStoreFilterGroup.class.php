<?php
/**
* Thrown when trying to create a filter with an invalid type
*
* @package Icinga_Api
* @category DataStoreModifier
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/

class InvalidFilterTypeException extends AppKitException {};
/**
* Filter that allows or/and grouping of other StoreFilterBase derivates
* For an example implementation, look at @see ApiStoreFilter
* @package Icinga_Api
* @category DataStoreModifier
*
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class GenericStoreFilterGroup extends StoreFilterBase implements Iterator {
    protected $type = "AND";
    protected $subFilters = array();
    /**
    * Possible types for filtering
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $possibleTypes = array(
                                   "and" => "AND",
                                   "or"=>"OR"
                               );

    /**
    * Returns the type of this filtergroup
    * @return String
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getType() {
        return $this->type;
    }

    /**
    * Sets the type of this filter
    * @param String The type of this filter
    * Throws InvalidFilterTypeException on error
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function setType($type) {
        if (isset($this->possibleTypes[$type])) {
            $this->type = $this->possibleTypes[$type];
        } else if (in_array($type,$this->getPossibleTypes())) {
            $this->type = $type;
        } else {
            throw new InvalidFilterTypeException("Type ".$type." is not supported");
        }
    }

    /**
    * Adds a filter/filtergroup to this group
    *
    * @param StoreFilterBase    The filter to add
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function addSubFilter(StoreFilterBase $b) {
        $this->subFilters[] = $b;
    }

    /**
    * Returns all filters in this group
    *
    * @return Array An array of @see StoreFilterBases
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getSubFilters() {
        return $this->subFilters;
    }

    /**
    * Returns possible types for this filtergroup
    * @return Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getPossibleTypes() {
        return $this->possibleTypes;
    }

    /**
    * @see IDataStoreModifier::__getJSDescriptor
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
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
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public static function parse($filter,$parser, $instance=null) {
        $o;

        if ($instance) {
            $o = new $instance();
        } else {
            $o = new self();
        }

        if (!isset($filter["type"]) || !isset($filter["items"])) {
            return null;
        }

        if (!in_array($filter["type"],$o->getPossibleTypes()) || empty($filter["items"])) {
            return null;
        }

        $o->setType($filter["type"]);
        $availableItems = array();
        foreach($filter["items"] as $item) {
            $subFilter = $parser->tryParse($item);

            if ($subFilter instanceof StoreFilterBase) {
                $o->addSubFilter($subFilter);
            }
        }

        return $o;
    }

    /**
    * Creates a new Filtergroup
    * @param    String  The type of this filter
    * @param    Array   Filters contained by this filtergroup
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __construct($type = null, array $subFilters = array()) {
        if ($type) {
            $this->setType($type);
        }

        foreach($subFilters as $subFilter) {
            $this->addSubFilter($subFilters);
        }
    }

    /**
    * Returns an array defining this filter
    * array(
    *   "type"      => The type of this filter
    *   "filters"   => The filterdefinitions @see GenericStoreFilter::__toArray
    * )
    * @return Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __toArray() {
        $group = array("type"=>$this->getType(),"filters"=>array());
        foreach($this as $filter) {
            $group["items"][] = $filter->__toArray();
        }
        return $group;
    }
    /**
    * Returns this filter in json definition, @see __toArray
    * @return String
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __toString() {
        return json_encode($this->__toArray());
    }

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
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function key() {
        return $this->type;
    }

}
