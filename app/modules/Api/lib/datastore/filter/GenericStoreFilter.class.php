<?php

class InvalidStoreFilterException extends AppKitException {}

/**
* GenericStoreFilter
* Container classes for filtering and apiProvider parsing.
*
* Fields for the apiProvider parser must be defined in the subclasses.
* Because we need compatibility for PHP <5.3 we can't use the nice
* way via late static binding, so these fields are abstract and will be called
* from object instances instead.
*
* @package Icinga_Api
* @category DataStoreModifier
*
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
abstract class GenericStoreFilter extends StoreFilterBase {
    /**
    * The possible filter fields lie here, but should only be accessed
    * via the addFilterField method
    * @var fields
    * @private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $__fields = array();

    protected $field;
    protected $value;
    protected $operator;

    /**
    * Creates a new filter
    * @param String    The filter field to set
    * @param String    The operator for the filter
    * @param mixed     The value that will be set for filtering
    *
    * Throws an instance of InvalidStoreFilterException   If validation fails
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __construct($field = null,$operator= null,$value=null) {
        if ($field == null) {
            return;
        }

        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
        $errors = $this->checkIfValid();

        if ($errors !== true) {
            throw new InvalidStoreFilterException($errors);
        }

        $this->customFormatter();
    }

    /**
    * Adds a filterable field to this StoreFilter
    * @param GenericFilterField The filter to add
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function addFilterField(StoreFilterField $filter) {
        $this->__fields[] = $filter;
    }
    /**
    * Returns which fields types are supported by this filter.
    * @access private
    * Only works if initFieldDefinition is called, bloating up this class
    * @TODO:    If a change to PHP 5.3. can be performed and late static binding is available, put the parsing system in a static context.
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function getPossibleFields() {
        return $this->__fields;
    }

    /**
    * Try to parse a filter definition (in most cases a json object) and return
    * an instance of this object if it succeedes, otherwise null
    * @param mixed  The filter definition
    * @param DataStoreModifier  The parser that called this element
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public static function parse($filter,$parse) {
        return null;
    }

    /**
    * The creation of @see GenericFilterField is done here in our subclasses
    * @access private
    * Should be used for client-side api generation
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    abstract public function initFieldDefinition();

    /**
    * Custom function for customized filter validity checks.
    * Returns true if no errors occured or a String that will be thrown as
    * an InvalidStoreFilterException on error
    *
    * @return Boolean/String    True if valid, errormessage instead
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function checkIfValid() {
        return true;
    }

    /**
    * If any transformations on the field, operator or value should be performed
    * they can be applied here in the filter subclass
    *
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function customFormatter() {}

    /**
    * Returns an associative array with this filter, defining filter,value and operator fields
    * array(
    *    "field" => The target field of this filter
    *    "value" => The value to filter with
    *    "operator" => The operator to use for filtering
    * )
    *
    * @return array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __toArray() {
        return array(
                   "field" => $this->field,
                   "value" => is_array($this->value) ? implode($this->value) : $this->value,
                   "operator" => $this->operator
               );
    }

    /**
    * Returns this filter in it's json interpretation (for field definition
    * @see __toArray()
    * @return String    An json encoded string of this filter
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __toString() {
        return json_encode($this->__toArray());
    }

    /**
    * @see IDataStoreModifier::__:getJSDescriptor
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __getJSDescriptor() {
        if (empty($this->__fields)) {
            $this->initFieldDefinition();
        }

        $fields = array();
        foreach($this->__fields as $field) {
            $fields[] = $field->__toArray();
        }

        return array("type"=>"atom", "filter"=>$fields);
    }
}
