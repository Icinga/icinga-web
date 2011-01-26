<?php

class IcingaApiRequestDescriptor {
    protected $table;
    protected $columns = array();
    protected $groupBy = array();
    protected $filter = null;
    
    public function getTable() {
	return $this->table;
    }
    public function getColumn() {
	return $this->column;
    }
    public function getGroupBy() {
	return $this->groupBy;
    }
    public function getFilter() {
	return $this->filter;
    }

    public function addColumn($columnName,$aggregate = null,$result) {

    }

    public function addLogicColumn(array $columns,$format, $resultName) {

    }

    public function
}
?>
