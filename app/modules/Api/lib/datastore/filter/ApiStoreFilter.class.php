<?php
/**
* Filter that operates on (Icinga)Doctrine_Query objects and allows to filter them.
*
* @package Icinga_Api
* @category DataStoreModifier
*
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class ApiStoreFilter extends GenericStoreFilter {
    /**
    * returns the filter definitions. These are mainly needed by the api-creation mechanismn
    * - invalid field/value combinations should be handled by the validator
    * @var Array
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected $filterFields = array();

    /**
    * @see GenericStoreFilter::parse
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public static function parse($filter,$parser) {
        if (!isset($filter["field"]) || !isset($filter["operator"]) || !isset($filter["value"])) {
            return null;
        } else {
            return new self($filter['field'],$filter['operator'],$filter['value']);
        }
    }

    /**
    * @access private
    * Iterates through filterFields and registers defined filterfields ( currentlyn
    * used for export to client)
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function initFieldDefinition() {
        $filters = $this->filterDefs;
        foreach($filters as $filter) {
            $f = new StoreFilterField();
            $f->displayName = $filter["display_name"];
            $f->name = $filter["field"];
            $f->operators = $filter["operators"];
            $f->possibleValues = $filter["possibleValues"];
            $this->addFilterField($f);
        }
    }

    /**
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function checkIfValid() {
        return true; //as the filter def is growing large validity should be checked by the validator
    }

    /**
    * @access private
    * Formats the value in this Filter and adds quotes if necessary
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    private function formatValues(Doctrine_Connection $c) {
        $value = array();

        if (is_array($this->value)) {
            foreach($this->value as $v) {
                if (!is_numeric($v)) {
                    $v = $c->quote($v,"string");
                }

                $value[] = $c->quote($v,"decimal");
            }
            return $value;
        } else {
            return $c->quote($this->value,"string");
        }
    }

    /**
    * Applies this filter to a IcingaDoctrine_Query or exports it (depending
    * if the $dqlOnly is set)
    *
    * @param IcingaDoctrine_Query   The query to apply this filter to
    * @param Boolean    True to only return the dql
    *
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly) {
        $connection = $q->getConnection();
        $v = $this->formatValues($connection);
        $dql = $connection->quoteIdentifier($this->field)." ".$this->operator;

        if (is_array($v)) {
            $dql .= " (".implode(",",$v).")";
        } else {
            $dql .= " ".$v;
        }

        if ($dqlOnly) {
            return $dql;
        } else {
            $q->where($dql);
        }

        return;
    }
}


