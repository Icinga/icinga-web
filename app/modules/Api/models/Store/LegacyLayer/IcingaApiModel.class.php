<?php
/**
* Emulates the behaviour of the deprecated icinga-api, should be removed 
* when migration to doctrine is finished on the frontend/backend
*
* @package Icinga_Api
* @category LegacyLayer
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/ 

class Api_Store_Legacy_IcingaApiModel extends AbstractDataStoreModel  {
    protected $apiValues = array(
        "SearchTarget"   => "",
        "Grouping"       => "",
        "Order"          => "DESC",
        "OrderColum"     => "DESC",
        "Limit"          => 0,
        "ResultType"     => IcingaApiSearch::RESULT_ARRAY
        "Filter"         => array(),
        "Columns"        => array(),
        "ConfigType"     => 1,
        "IsCount"     => false 
    );

    public function setSearchTarget($target) {
        $this->apiValues["SearchTarget"] = $target;
    }
    public function setGrouping($target) {
        $this->apiValues["SearchTarget"] = $target;
    }
    public function setSearchOrder($column,$direction) {
        $this->apiValues["SearchColumn"] = $column;
    }
    public function setLimit($target) {
        $this->apiValues["SearchTarget"] = $target;
    }
    public function setSearchType($type) {
        if($type == IcingaApiSearch::SEARCH_TYPE_COUNT)
            $this->apiValues["IsCount"] = true;
        else 
            $this->apiValues["IsCount"] = false;
    }
    public function setResultColumns($cols) {
        $this->apiValues["Columns"] = $cols;
    }
    public function addSetSearchFilter($filter) {
        $this->apiValues["Filter"] = $filter;
    }
    public function setResultType($target) {
        $this->apiValues["SearchTarget"] = $target;
    }
    public function setConfigType($type) {
        $this->apiValues["ConfigType"] = $type;
    }


}

class Api_ApiLegacyLayerModel extends ApiDataRequestBaseModel {

    public function createSearch() {
        return new $this->searchWrapper();
    } 

} 


