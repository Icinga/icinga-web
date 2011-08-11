<?php
/**
* Modifier that handles Pagination (offset/limit) and extends the DataStore by the
* setOffset and setLimit functions
*
* @package Icinga_Api
* @category DataStore
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class Api_Store_Modifiers_StorePaginationModifierModel extends IcingaBaseModel implements IDataStoreModifier {
    /**
    * Returns an array with the parameter name=>function mapping for JS export
    * @return Array     An associative array with name=>value mapping
    **/
    public function getMappedArguments() {
        return array(
                   "offset" => "offset",
                   "limit" => "limit"
               );
    }

    protected $offset = 0;
    protected $limit = -1;

    /**
    *   Sets the offset value for the query
    *   @param integer $offset
    *
    *   Throws an instance of InvalidArgumentException If the offset is <0 or not numeric
    **/
    public function setOffset($offset) {
        $this->offset = $this->checkVal($offset);
    }

    /**
    *   Sets the limit value for the query
    *   @param integer $offset
    *
    *   Throws an instance of InvalidArgumentException If the limit is <0 or not numeric
    **/
    public function setLimit($limit) {
        $this->limit = $this->checkVal($limit);
    }

    /**
    *  Returns the offset of this modifier
    *  @return Integer
    **/
    public function getOffset() {
        return $this->offset;
    }

    /**
    *  Returns the limit this modifier sets
    *  @return Integer
    **/
    public function getLimit() {
        return $this->limit;
    }

    /**
    * @access private
    **/
    private function checkVal($val) {
        if (!is_numeric($val)) {
            throw new InvalidArgumentException("Filter/Offset must be an integer");
        }

        if (!intval($val)<0) {
            throw new InvalidArgumentException("Filter/Offset must be an integer >= 0");
        }

        return intval($val);
    }
    /**
    * @see IDataStoreModifier::handleArgument
    **/
    public function handleArgument($name,$value) {
        switch ($name) {
            case 'offset':
                $this->offset = $this->checkVal($value);
                break;

            case 'limit':
                $this->limit = $this->checkVal($value);
                break;
        }
    }
    /**
    * @see IDataStoreModifier::modify
    **/
    public function modify(&$o) {
        $this->modifyImpl($o); // type safe call
    }

    /**
    * Typesafe call to modify
    * @access private
    **/
    protected function modifyImpl(Doctrine_Query &$o) {
        $o->offset($this->offset);

        if ($this->limit>0) {
            $o->limit($this->limit);
        }
    }
    /**
    * @see IDataStoreModifier::__getJSDescriptor
    **/
    public function __getJSDescriptor() {
        return array(
                   "type"=>"pagination",
                   "params" => $this->getMappedArguments()
               );
    }

}
?>
