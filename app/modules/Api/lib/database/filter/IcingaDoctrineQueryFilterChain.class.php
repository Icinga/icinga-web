<?php 

class IcingaDoctrineQueryFilterChain implements IcingaIDoctrineQueryFilter {
    
    /**
     * @var ArrayObject
     */
    private $filters = null;
    
    private $postRun = false;
    
    private $preRun = false;
    
    public function __construct() {
        $this->filters = new ArrayObject();
    }
    
    public function add(IcingaIDoctrineQueryFilter $filter) {
        $this->filters[] = $filter;
    }
    
    public function remove(IcingaDoctrineQueryFilterChain $filter) {
        foreach ($this->filters as $fid=>$checkFilter) {
            if ($checkFilter === $filter) {
                $this->filters->offsetUnset($fid);
                return true;
            }
        }
        return false;
    }
    
    public function hasFilters() {
        return $this->filters->count() ? true : false;
    }
    
    public function preQuery(Doctrine_Query_Abstract $query) {
        foreach ($this->filters as $filter) {
            $filter->preQuery($query);
        }
        $this->preRun = true;
    }
    
    public function postQuery(Doctrine_Query_Abstract $query) {
        foreach ($this->filters as $filter) {
            $filter->postQuery($query);
        }
        $this->postRun = true;
    }
    
    public function canExecutePre() {
        return !$this->preRun && $this->hasFilters();
    }
    
    public function canExecutePost() {
        return !$this->postRun && $this->hasFilters();
    }
}

