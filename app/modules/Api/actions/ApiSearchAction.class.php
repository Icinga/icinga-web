<?php

class Api_ApiSearchAction extends IcingaApiBaseAction {
    /**
     * Returns the default view if the action does not serve the request
     * method used.
     *
     * @return     mixed <ul>
     *                     <li>A string containing the view name associated
     *                     with this action; or</li>
     *                     <li>An array with two indices: the parent module
     *                     of the view to be executed and the view to be
     *                     executed.</li>
     *                   </ul>
     */

    static public $defaultColumns = array(
                                        "instance" => array("INSTANCE_ID",'INSTANCE_NAME','INSTANCE_DESCRIPTION'),
                                        "host" => array("HOST_ID",'HOST_OBJECT_ID','HOST_INSTANCE_ID',"HOST_NAME","HOST_ALIAS","HOST_DISPLAY_NAME","HOST_ADDRESS","HOST_IS_ACTIVE"),
                                        "service" => array("SERVICE_ID","SERVICE_OBJECT_ID","SERVICE_IS_ACTIVE","SERVICE_INSTANCE_ID","SERVICE_NAME","SERVICE_DISPLAY_NAME","SERVICE_OUTPUT","SERVICE_PERFDATA"),
                                        "hostgroup" => array("HOSTGROUP_ID","HOSTGROUP_OBJECT_ID","HOSTGROUP_INSTANCE_ID","HOSTGROUP_NAME","HOSTGROUP_ALIAS"),
                                        "servicegroup" => array("SERVICEGROUP_ID","SERVICEGROUP_OBJECT_ID","SERVICEGROUP_INSTANCE_ID","SERVICEGROUP_NAME","SERVICEGROUP_ALIAS"),
                                        "customvariable" => array("CUSTOMVARIABLE_ID","CUSTOMVARIABLE_OBJECT_ID","CUSTOMVARIABLE_INSTANCE_ID","CUSTOMVARIABLE_NAME","CUSTOMVARIABLE_VALUE","CUSTOMVARIABLE_MODIFIED","CUSTOMVARIABLE_UPDATETIME"),
                                        "contact" => array("CONTACT_NAME","CONTACT_CUSTOMVARIABLE_NAME","CONTACT_CUSTOMVARIABLE_VALUE"),
                                        "contactgroup" => array("CONTACTGROUP_ID","CONTACTGROUP_OBJECT_ID","CONTACTGROUP_INSTANCE_ID","CONTACTGROUP_NAME","CONTACTGROUP_ALIAS"),
                                        "timeperiod" => array("TIMEPERIOD_ID","TIMEPERIOD_OBJECT_ID","TIMEPERIOD_INSTANCE_ID","TIMEPERIOD_NAME","TIMEPERIOD_ALIAS","TIMEPERIOD_DAY","TIMEPERIOD_STARTTIME","TIMEPERIOD_ENDTIME"),
                                        "hoststatus" => array(),
                                        "servicestatus" => array(),
                                        "hosttimes" => array(),
                                        "servicetimes" => array(),
                                        "config" => array("CONFIG_VAR_ID","CONFIG_VAR_INSTANCE_ID","CONFIG_VAR_NAME","CONFIG_VAR_VALUE"),
                                        "program" => array(),
                                        "log" => array("LOG_ID","LOG_INSTANCE_ID","LOG_TIME","LOG_ENTRY_TIME","LOG_ENTRY_TIME_USEC","LOG_TYPE","LOG_DATA","LOG_REALTIME_DATA","LOG_INFERRED_DATA"),
                                        "host_status_summary" => array("HOST_ID",'HOST_STATUS_ALL','HOST_STATE','HOST_STATE_COUNT','HOST_LAST_CHECK'),
                                        "service_status_summary" => array("SERVICE_ID",'SERVICE_STATUS_ALL','SERVICE_OUTPUT','SERVICE_LONG_OUTPUT','SERVICE_PERFDATA','SERVICE_LAST_CHECK'),
                                        "host_status_history" => array("STATEHISTORY_ID","STATEHISTORY_INSTANCE_ID","STATEHISTORY_STATE_TIME","STATEHISTORY_OBJECT_ID","STATEHISTORY_STATE_CHANGE","STATEHISTORY_STATE","STATEHISTORY_OUTPUT","STATEHISTORY_LONG_OUTPUT"),
                                        "service_status_history" => array("STATEHISTORY_ID","STATEHISTORY_INSTANCE_ID","STATEHISTORY_STATE_TIME","STATEHISTORY_OBJECT_ID","STATEHISTORY_STATE_CHANGE","STATEHISTORY_STATE","STATEHISTORY_OUTPUT","STATEHISTORY_LONG_OUTPUT"),
                                        "host_parents" => array("HOST_ID",'HOST_OBJECT_ID',"HOST_NAME","HOST_PARENT_OBJECT_ID","HOST_PARENT_NAME"),
                                        "notifications" => array("NOTIFICATION_ID","NOTIFICATION_TYPE","NOTIFICATION_REASON","NOTIFICATION_STARTTIME","NOTIFICATION_ENDTIME","NOTIFICATION_OUTPUT","NOTIFICATION_OBJECT_ID","NOTIFICATION_OBJECTTYPE_ID"),
                                        "hostgroup_summary" => array('HOSTGROUP_SUMMARY_COUNT',"HOSTGROUP_ID","HOSTGROUP_OBJECT_ID","HOSTGROUP_NAME"),
                                        "comment" => array('SERVICEGROUP_SUMMARY_COUNT',"SERVICEGROUP_ID","SERVICEGROUP_OBJECT_ID","SERVICEGROUP_NAME"),
                                        "servicecomment" => array('SERVICE_NAME',"SERVICE_ID","COMMENT_ID","COMMENT_DATA"),
                                        "hostcomment" => array('HOST_NAME',"HOST_ID","COMMENT_ID","COMMENT_DATA"),
                                        "host_service" => array('HOST_NAME',"SERVICE_NAME")
                                    );


    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $rd) {

        if (!$this->context->getUser()->isAuthenticated() 
            || $this->getContainer()->getAttribute('success', 'org.icinga.api.auth') !== true) {
            return array('Api', 'GenericError');
	    }
        
        $context = $this->getContext();
        $API = $context->getModel("Icinga.ApiContainer","Web");
        $target = $rd->getParameter("target");

        $search = @$API->createSearch()->setSearchTarget($target);
        $this->addFilters($search,$rd);

        $this->setColumns($search,$rd);
        $this->setGrouping($search,$rd);
        $this->setOrder($search,$rd);
        $this->setLimit($search,$rd);

        $search->setResultType(IcingaApiConstants::RESULT_ARRAY);
        // Adding security principal targets to the query
        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);
        
        $res = $search->fetch()->getAll();

        //Setup count
        if ($rd->getParameter("countColumn")) {
            $search = @$API->createSearch()->setSearchTarget($target);
            $this->addFilters($search,$rd);

            $this->setColumns($search,$rd);
            $this->setGrouping($search,$rd);
            $this->setOrder($search,$rd);
            $this->setLimit($search,$rd);

            $search->setSearchType(IcingaApiConstants::SEARCH_TYPE_COUNT);

            IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);
           
            $rd->setParameter("searchCount",$search->fetch()->getAll());
        }
        
        if($rd->getParameter("withSLA") && ($target == "host" || $target == "service")) {
            $slaDefaults = AgaviConfig::get("modules.api.sla_settings");

            if(isset($slaDefaults["enabled"]) && $slaDefaults["enabled"]) {
                if(!isset($slaDefaults["default_timespan"]))
                    $slaDefaults["default_timespan"] = "-1 Month";
                $ts = $rd->getParameter("slaTimespan",$slaDefaults["default_timespan"]);
                $this->addSLAData($res,$ts);
            }
        }
        
        $rd->setParameter("searchResult", $res);

        return $this->getDefaultViewName();
    }
    private function addSLAData(array &$result,$timespan) {
        $objIds = array();
        $map = array(); //hashmap for fast lookup
        foreach($result as $idx=>&$record) {
            $id = "";
            if(isset($record["HOST_OBJECT_ID"]))
                $id = $record["HOST_OBJECT_ID"];
            else if (isset($record["SERVICE_OBJECT_ID"]))
                $id = $record["SERVICE_OBJECT_ID"];
            else
                continue;
            $record["SLA_STATE_AVAILABLE"] = 0;
            $record["SLA_STATE_UNAVAILABLE"] = 0;
            $record["SLA_STATE_0"] = 0;
            $record["SLA_STATE_1"] = 0;
            $record["SLA_STATE_2"] = 0;
            $record["SLA_STATE_3"] = 0;


            $map[$id] = $idx;
            $objIds[] = $id;
        }
        $filter = $this->getContext()->getModel("SLA.SLAFilter","Api");
        $filter->setObjectId($objIds);
        
        $filter->setTimespan($timespan);

        $stmt = IcingaSlahistoryTable::getSummary(null, $filter);

        foreach($stmt as $sla_entry) {
            $oid = $sla_entry->object_id;
            $state = $sla_entry->sla_state;
            if(!isset($map[$oid]))
                continue;
            $entry = &$result[$map[$oid]];
            
            if(($state > 0 && $sla_entry->objecttype_id == 1) ||
                ($state > 1 && $sla_entry->objecttype_id == 2))
                $entry["SLA_STATE_UNAVAILABLE"] += $sla_entry->percentage;
            else
                $entry["SLA_STATE_AVAILABLE"] += $sla_entry->percentage;
            if(isset($entry["SLA_STATE_".$state]))
                $entry["SLA_STATE_".$state] += $sla_entry->percentage;

           
        }

    }
    protected function addFilters($search,AgaviRequestDataHolder $rd) {
        // URL filter definitions
        $field = $rd->getParameter("filter",null);

        if (empty($field)) {
            $field = json_decode($rd->getParameter("filters_json"),true);
        }

        if (!empty($field)) {
            $search->setSearchFilter($this->buildFiltersFromArray($search,$field));
        }

        // POST filter definitions
        $advFilter = $rd->getParameter("filters",array());
        if(!is_array($advFilter))
            $advFilter = array($advFilter);
        foreach($advFilter as $fl) {
            $fl["value"] = str_replace("*","%",$fl["value"]);
            $search->setSearchFilter($fl["column"],$fl["value"],$fl["relation"]);
        }
    }

    public function buildFiltersFromArray($search, array $filterdef) {
        $searchField = $filterdef;

        if (isset($filterdef["type"])) {
            $searchField = $filterdef["field"];
        }
       
        $filterGroup = $search->createFilterGroup($filterdef["type"]);
        if(!is_array($searchField))
            $searchField = array($searchField);
        foreach($searchField as $element) {
            if ($element["type"] == "atom") {
                $element["value"] = str_replace("*","%",$element["value"]);
                $filterGroup->addFilter($search->createFilter(strtoupper($element["field"][0]),$element["value"][0],$element["method"][0]));
            } else {
                $filterGroup->addFilter($this->buildFiltersFromArray($search,$element));
            }
        }

        return $filterGroup;
    }


    public function setGrouping($search,AgaviRequestDataHolder $rd) {
        $groups = $rd->getParameter("groups",array());

        if (!is_array($groups)) {
            $groups = array($groups);
        }

        if (!empty($groups)) {
            $search->setSearchGroup($groups);
        }
    }

    public function setOrder($search,AgaviRequestDataHolder $rd) {
        $order_col = $rd->getParameter("order_col",null);
        $order_dir = $rd->getParameter("order_dir",'asc');

        if ($order_col) {
            $search->setSearchOrder($order_col,$order_dir);
        }

        $order = $rd->getParameter("order",array());

        if (!is_array($order)) {
            $order = array($order);
        }

        if (!empty($order)) {
            $search->setSearchOrder($order["column"],$order["dir"]);
        }
    }

    public function setColumns($search,AgaviRequestDataHolder $rd) {
        if ($search->getSearchType() == IcingaApiConstants::SEARCH_TYPE_COUNT) {
            $search->setResultColumns($rd->getParameter("countColumn"));
            return true;
        }
        
        $columns = $rd->getParameter("columns",null);
        if(!is_array($columns))
            $columns = array($columns);
        $columns_result = array();
        foreach($columns as $column) {
            $column = preg_replace("/[^1-9_A-Za-z]/","",$column);
            $column = strtoupper($column);
            if($column)
                $columns_result[] = $column;
        }
         
        if (!is_null($columns_result) && !empty($columns_result)) {
            
            $search->setResultColumns($columns_result);
        } else {            
            $search->setResultColumns(self::$defaultColumns[$rd->getParameter("target")]);
        }
    }

    public function setLimit($search,AgaviRequestDataHolder $rd) {
        $start = $rd->getParameter("limit_start",0);
        $limit = $rd->getParameter("limit",null);

        if ($limit > 0) {
            $search->setSearchLimit($start,$limit);
        }
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        return $this->executeRead($rd);
    }
}

?>
