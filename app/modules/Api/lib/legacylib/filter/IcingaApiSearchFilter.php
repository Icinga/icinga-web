<?php
/**
 * Filter class to restrict results in the SQL Query.
 * There are no more placeholders in the query anymore, the values will be directly set in the
 * createQueryStatement function.
 *
 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
 */
abstract class IcingaApiSearchFilter implements IcingaApiSearchFilterInterface {
    protected $field;
    protected $match = IcingaApiConstants::MATCH_EXACT;
    protected $value;
    public $search = null;

    /**
     * Direct construction is not allowed, as this should be managed by the search creating the filter
     * Use the IcingaApiSearch::createSearchFilter function to create filters
     * Shouldn't be called directly, use getInstance
     * @return IcingaApiSearchFilter
     *
     * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
     */
    public function __construct() {}

    /**
     * The field on which this filter acts
     * @param String $field
     * @throws IcingaApiSearchException
     *
     * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
     */
    public function setField($field) {
        // Resolve abstract filter name to column name
        if (method_exists($this->search,"getColumn")) {
            $res_field = $this->search->getColumn($field);

            if ($res_field !== false) {
                $this->field = $res_field;
            } else {
                throw new IcingaApiSearchException('setSearchFilter(): Unknown result column "'.$field.'"!');
            }
        } else {
            $this->field = $field;
        }
        $this->field == strtoupper(preg_replace("/[^1-9_A-Za-z]/","",$this->field));
    }

    public function getField() {
        return $this->field;
    }

    public function setMatch($match) {
        $this->match = $match;
    }
    public function getMatch() {
        return $this->match;
    }

    public function setValue($val) {
        $this->value = $val;
    }
    public function getValue() {
        return $this->value;
    }


    abstract public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly = false);

    public function getAllFilterColumns() {
        return array($this->field);
    }
    /**
     * Creates an IcingaApiSearchFilter instance for the corresponding data target and returns it.
     *
     * @param IcingaApiSearch $search The search performing this action
     * @param String $field Defines a column on creation
     * @param String $value Defines a value on creation
     * @param String $match Defines a operator/match type on creation
     *
     * @return IcingaApiSearchFilter A subclass of IcignaApiSearchFilter
     * 	 *
     * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
     */
    public static function createInstance($search,$field = null,$value = null,$match = null) {

        $class = 'IcingaApiSearchFilterIdo';

        $filter = new $class;
        $filter->search= $search;

        // set default params if given
        if (!is_null($field)) {
            $filter->setField($field);
        }

        if (!is_null($value)) {
            $filter->setValue($value);
        }

        if (!is_null($match)) {
            $filter->setMatch($match);
        }

        return $filter;

    }
}


?>
