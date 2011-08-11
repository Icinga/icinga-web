<?php
/**
 * Grouped filter for extended search conditions, like (field1 AND field2 OR (field3))
 *
 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
 */

abstract class IcingaApiSearchFilterGroup implements IcingaApiSearchFilterInterface, Iterator, IDataStoreModifier {
    protected $filters = array();
    protected $type = IcingaApiConstants::SEARCH_AND;

    /**
     * Creates a new IcingaApiSearchFilterGroup instance
     * @param array $filters IcingaApiSearchFilters to be added on creation
     * @param string $type The filter chaining type (AND/OR)
     *
     */
    public function __construct(array $filters = array(), $type = null) {
        if ($type) {
            $this->setType($type);
        }

        foreach($filters as $filter) {
            $this->addFilter($filter);
        }
    }
    public function clear() {
        $this->filters = array();
    }

    /**
     * Adds a filter/filtergroup to this filtergroup
     * Duplicated filters will be detected and only added once
     *
     * @param IcingaApiSearchFilterInterface $filter The filter to add
     */
    public function addFilter(IcingaApiSearchFilterInterface $filter) {
        if (in_array($filter,$this->filters)) {
            return true;
        }

        // check for filters that do the same
        if ($filter instanceof IcingaApiSearchFilter) {
            foreach($this->filters as $filter_existing) {
                if (!$filter_existing instanceof IcingaApiSearchFilter) {
                    continue;
                }

                if ($filter_existing->getMatch() == $filter->getMatch() &&
                    $filter_existing->getField() == $filter->getField() &&
                    $filter_existing->getValue() == $filter->getValue()) {
                    return false;
                }
            }
        }

        $this->filters[] = $filter;
    }

    /**
     * Batch operator for @see addFilter
     * @param array $filters A array of IcingaApiSearchFilterInterface instances
     */
    public function addFilters(array $filters) {
        foreach($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * Removes a filter from the list
     * @param mixed $indexOrFilter either a filter instance or an index of the filter
     * @return bool Success
     */
    public function removeFilter($indexOrFilter) {
        if (is_int($indexOrFilter)) { // check index
            unset($this->filters[$index]);
            return true;
        } else if ($indexOrFilter instanceof IcingaApiSearchFilterInterface) { // check object
            $idx = array_search($indexOrFilter,$this->filters,true);

            if ($idx === false) {


                return false;
            } else {
                unset($this->filters[$idx]);
                return true;
            }
        }

        return false;
    }

    public function getFilters() {
        return $this->filters;
    }
    public function clearFilters() {
        $this->filters = array();
    }

    public function setType($type) {
        $this->type = $type;
    }
    public function getType() {
        return $this->type;
    }

    public function next() {
        next($this->filters);
    }
    public function rewind() {
        reset($this->filters);
    }
    public function current() {
        return current($this->filters);
    }
    public function key() {
        return key($this->filters);
    }
    public function valid() {
        return !is_null(key($this->filters));
    }

    public function getAllFilterColumns() {
        $fields = array();
        foreach($this as $filter)
        $fields = array_intersect($fields,$filter->getAllFilterColumns());

        return $fields;
    }


    public function handleArgument($name,$value) {}
    public function __getJSDescriptor() {}
    public function modify(&$o) {
        $this->modifyImpl($o);
    }

    public function modifyImpl(IcingaDoctrine_Query $q) {
        $this->__toDQL($q);
    }
    abstract public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly = false);
    public function getMappedArguments() {}
    /**
     * Static method to create a instance of this filtergroup suiting to the databackend (ido, for example)
     * @param IcingaApiSearch $search The search this filter will operator on
     * @param String $type The filtergroup type (and/or)
     * @return IcingaApiSearchFilterGroup
     *
     * @throws IcingaApiException
     */
    public static function createInstance($search, $type = IcingaApiConstants::SEARCH_AND) {

        $class = 'IcingaApiSearchFilterGroupIdo';

        try {
            $filterGroup = new $class;

            if ($type) {
                $filterGroup->setType($type);
            }

            return $filterGroup;
        } catch (Exception $e) {
            throw new AppKitException("Filtergroup implementation of ".$type." doesn't exist!");
        }
    }
}

?>
