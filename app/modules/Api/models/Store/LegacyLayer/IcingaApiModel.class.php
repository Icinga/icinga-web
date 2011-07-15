<?php
/**
* Emulates the behaviour of the deprecated icinga-api, should be removed 
* when migration to doctrine is finished on the frontend/backend
*
* @package Icinga_Api
* @category LegacyLayer
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/ 
interface IcingaApiConstants {

	// CONNECTION TYPES
	const CONNECTION_IDO = 'Ido';
	const CONNECTION_IDO_ABSTRACTION = 'IdoAbstraction';
	const CONNECTION_FILE = 'File';
	const CONNECTION_LIVESTATUS = 'Livestatus';

	// CONTACT SOURCES
	const CONTACT_SOURCE_PHP_AUTH_USER = 'PHP_AUTH_USER';

	// DEBUGGING
	const DEBUG_OVERALL_TIME = 'overall time';
	const DEBUG_LEVEL_ALL = 0xff;
	const DEBUG_LEVEL_ERROR = 0x01;
	const DEBUG_LEVEL_WARNING = 0x02;
	const DEBUG_LEVEL_DEBUG = 0x08;
	
	// FILE SOURCES
	const FILE_OBJECTS = 'objects';
	const FILE_RETENTION = 'retention';
	const FILE_STATUS = 'status';

	// TARGET TYPES
	const TARGET_INSTANCE = 'instance';
	const TARGET_HOST = 'host';
	const TARGET_SERVICE = 'service';
	const TARGET_HOSTGROUP = 'hostgroup';
	const TARGET_SERVICEGROUP = 'servicegroup';
	const TARGET_CONTACT = 'contact';
	const TARGET_CONTACTGROUP = 'contactgroup';
	const TARGET_TIMEPERIOD = 'timeperiod';
	const TARGET_HOSTSTATUS = 'hoststatus';
	const TARGET_SERVICESTATUS = 'servicestatus';
	const TARGET_CUSTOMVARIABLE = 'customvariable';
	const TARGET_HOST_TIMES = 'hosttimes';
	const TARGET_SERVICE_TIMES = 'servicetimes';
	const TARGET_CONFIG = 'config';
	const TARGET_PROGRAM = 'program';
	const TARGET_LOG = 'log';
	const TARGET_HOST_STATUS_SUMMARY = 'host_status_summary';
	const TARGET_SERVICE_STATUS_SUMMARY = 'service_status_summary';
	const TARGET_HOST_STATUS_HISTORY = 'host_status_history';
	const TARGET_SERVICE_STATUS_HISTORY = 'service_status_history';
	const TARGET_HOST_PARENTS = 'host_parents';
	const TARGET_NOTIFICATIONS = 'notifications';
	const TARGET_HOSTGROUP_SUMMARY = 'hostgroup_summary';
	const TARGET_SERVICEGROUP_SUMMARY = 'servicegroup_summary';
	const TARGET_COMMAND = 'command';	// livestatus only
	const TARGET_DOWNTIME = 'downtime';
	const TARGET_DOWNTIMEHISTORY = 'downtimehistory';
	const TARGET_COMMENT = 'comment';
	const TARGET_STATUS = 'status';		// livestatus only
	const TARGET_HOST_SERVICE = 'host_service';
	// SEARCH TYPES
	const SEARCH_TYPE_COUNT = 'count';

	// SEARCH AGGREGATORS
	const SEARCH_OR = 'or';
	const SEARCH_AND = 'and';

	// MATCH TYPES
	const MATCH_EXACT = '=';
	const MATCH_NOT_EQUAL = '!=';
	const MATCH_LIKE = 'like';
	const MATCH_NOT_LIKE = 'not like';
	const MATCH_GREATER_THAN = '>';
	const MATCH_GREATER_OR_EQUAL = '>=';
	const MATCH_LESS_THAN = '<';
	const MATCH_LESS_OR_EQUAL = '<=';
	
	// RESULT TYPES
	const RESULT_OBJECT = 'object';
	const RESULT_ARRAY = 'array';

	// HOST STATES
	const HOST_STATE_OK = 0;
	const HOST_STATE_UNREACHABLE = 1;
	const HOST_STATE_DOWN = 2;

	// SERVICE STATES
	const SERVICE_STATE_OK = 0;
	const SERVICE_STATE_WARNING = 1;
	const SERVICE_STATE_CRITICAL = 2;
	const SERVICE_STATE_UNKNOWN = 3;

	// COMMAND INTERFACES
	const COMMAND_PIPE = 'Pipe';
	const COMMAND_SSH = 'Ssh';

	// COMMAND FIELDS
	const COMMAND_INSTANCE = 'instance';
	const COMMAND_HOSTGROUP = 'hostgroup';
	const COMMAND_SERVICEGROUP = 'servicegroup';
	const COMMAND_HOST = 'host';
	const COMMAND_SERVICE = 'service';
	const COMMAND_ID = 'id';
	const COMMAND_AUTHOR = 'author';
	const COMMAND_COMMENT = 'comment';
	const COMMAND_STARTTIME = 'starttime';
	const COMMAND_ENDTIME = 'endtime';
	const COMMAND_STICKY = 'sticky';
	const COMMAND_PERSISTENT = 'persistent';
	const COMMAND_NOTIFY = 'notify';
	const COMMAND_RETURN_CODE = 'return_code';
	const COMMAND_CHECKTIME = 'checktime';
	const COMMAND_FIXED = 'fixed';
	const COMMAND_OUTPUT = 'output';
	const COMMAND_PERFDATA = 'perfdata';
	const COMMAND_DURATION = 'duration';
	const COMMAND_DATA = 'data';
	const COMMAND_NOTIFICATION_OPTIONS = 'notification_options';
	const COMMAND_DOWNTIME_ID = 'downtime_id';



}
/*
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
*/
interface IcingaApiInterface {};
class LegacyApiResultModel {
    protected $data;
    
    public function __construct($data) {
        $this->data = is_int($data) ? array("count" =>array($data)) : $data;
    }
    public function getRow() {
        $d = current($this->data);
        next($this->data);
        return $d;
    }
    public function getAll() {
        if(is_object($this->data))
            return $this->data->toArray();
        else if(is_array($this->data))
            return $this->data;
    }

}

class ApiLegacyLayerCountObject {
    public function count() {
        return 1;
    }
    private $count;
    public function get($noMatterWhat) {
        return $this->count;
    }
    public function __construct($c) {
        $this->count = $c;
    }
}

class Api_Store_LegacyLayer_IcingaApiModel extends IcingaApiDataStoreModel implements IcingaApiInterface 
{
    protected $isCount = false;
    protected $resultType = Doctrine_Core::HYDRATE_ARRAY;
    public function setResultType($type) {
        if($type == IcingaApiConstants::RESULT_OBJECT)
            $this->setResultType("RECORD");
        if($type == IcingaApiConstants::RESULT_ARRAY)
            $this->setResultType("ARRAY");
        return $this;
    }
    protected function setupModifiers() {
        $this->registerStoreModifier("Store.LegacyLayer.TargetModifier","Api");  
        $this->registerStoreModifier("Store.Modifiers.StorePaginationModifier","Api");
        $this->registerStoreModifier("Store.Modifiers.StoreSortModifier","Api");
        $this->registerStoreModifier("Store.Modifiers.StoreGroupingModifier","Api");
    }
 
    public function createSearch() {
        $this->result = null;
        return $this;
    }
   
    public function setSearchOrder ($column, $direction = 'asc') { 
        $this->result = null;
        $this->setFields(array($column),true); 
        $column = $this->resolveColumnAlias($column);    
        $this->setSortfield($column);
        $this->setDir(strtoupper($direction)); 	
        return $this;
    }
	
    public function setSearchLimit ($start, $length = false) {
        $this->result = null;
        $this->setOffset($start);
        if($length)
            $this->setLimit($length);
        return $this;
    }

    public function setSearchTarget($target) {
        $this->result = null;
        parent::setSearchTarget($target);
        return $this;
    }
    
    public function setResultColumns($target) {
        $this->result = null;
        parent::setResultColumns($target);
        return $this;
    }

    public function execRead() {
        $request = $this->createRequestDescriptor();
        $this->applyModifiers($request); 
        $result = null;
        $this->lastQuery = $request;
      
        if(!$this->isCount)
            $result = $request->execute(NULL,$this->resultType);
        else
            $result = $request->count();
        return $result; 
    }
    private $result = null;
    
    public function fetch() {
        if($this->result)
            return $this->result;   

        $data =  $this->execRead();     
       
        if($this->isCount) {
            $fields = $this->getFields();
            $countField = explode(".",$fields[0],2);
            if(count($countField) > 1)
                $countField = $countField[1];
            $data = array(
                "0" => array("COUNT_".strtoupper($countField) => $data)
            );     
            
           
        }
        $this->result = $this->getContext()->getModel(
            "Store.LegacyLayer.LegacyApiResult","Api",array(
                "result" => $data 
            )
        );
        return $this->result;
    }
   

    public function setSearchFilter() {
        return $this;
    } 

   	public function setSearchGroup ($columns) {
		if (!is_array($columns)) {
			$columns = array($columns);
		}
        
        $this->setFields($columns,true); 
        foreach($columns as &$column) {
            $column = $this->resolveColumnAlias($column);    
        }
        $this->setGroupfields($columns);
        return $this;
    }

    public function setSearchType($type) {
        if($type == IcingaApiConstants::SEARCH_TYPE_COUNT) 
            $this->isCount = true;
        else
            $this->isCount = false;
        return $this;
    }   

} 


