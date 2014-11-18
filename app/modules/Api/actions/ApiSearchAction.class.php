<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
            "host_service" => array('HOST_NAME',"SERVICE_NAME"),
            'downtime' => array('DOWNTIME_ID', 'HOST_NAME', 'SERVICE_NAME', 'DOWNTIME_INTERNAL_DOWNTIME_ID')
    );

    /*
        the following array includes a list of fields
        we don't want to select with the query itself
        because they are big fields or BLOBs that breaks
        DISTINCT in a database query

        we will select them later in a extra query and
        then merge them into the resultset
        -mfrosch
    */
    public $blob_columns = array(
        // hoststatus
        'HOST_OUTPUT',
        'HOST_LONG_OUTPUT',
        'HOST_PERFDATA',
        // servicestatus
        'SERVICE_OUTPUT',
        'SERVICE_LONG_OUTPUT',
        'SERVICE_PERFDATA',
        // statehistory
        'STATEHISTORY_OUTPUT',
        'STATEHISTORY_LONG_OUTPUT',
    );
    public $selected_blob_columns = array();


    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviRequestDataHolder $rd) {

        if (!$this->context->getUser()->isAuthenticated()
                || $this->getContainer()->getAttribute('success', 'org.icinga.api.auth', true) == false) {
            return array('Api', 'GenericError');
        }

        $context = $this->getContext();
        $API = $context->getModel("Icinga.ApiContainer","Web");
        $target = $rd->getParameter("target");
        $connection = $rd->getParameter("connection","icinga");
        
        $this->context->getModel("DBALMetaManager","Api")->switchIcingaDatabase($connection);
        
        $search = @$API->createSearch($connection)->setSearchTarget($target);
        
        $this->addFilters($search,$rd);

        $this->setColumns($search,$rd);
        $this->setGrouping($search,$rd);
        $this->setOrder($search,$rd);
        $this->setLimit($search,$rd);

        $search->setResultType(IcingaApiConstants::RESULT_ARRAY);
        // Adding security principal targets to the query
        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);

        $res = $search->fetch()->getAll();
        
        // if we had blob columns in selection we want to join them
        if(count($this->selected_blob_columns) > 0) {
            // get ID list
            $columns_default = self::$defaultColumns[$rd->getParameter("target")];
            $idfield = $columns_default[0];
            $idlist = array();

            // iterate thru result and collect ids
            for($i = 0; $i < count($res); $i++) {
                if(isset($res[$i][$idfield])) {
                    $idlist[] = $res[$i][$idfield];
                }
            }

            // build search
            if(!empty($idlist)) {
                $columns = $this->selected_blob_columns;
                $columns[] = $idfield;

                $search = @$API->createSearch($connection)->setSearchTarget($target);
                $search->setDistinct(false); // force no distinct here - cause we are selecting BLOBs
                $search->setResultColumns($columns);
                $search->setSearchFilter($idfield, $idlist);

                $subres = $search->fetch()->getAll();

                // foreach result from the blobfield search
                foreach($subres as &$subrow) {
                    $id = $subrow[$idfield];

                    // check against each original resul
                    for($i=0; $i<count($res); $i++) {
                        $resRow = $res[$i];

                        // merge the data when matching
                        if($resRow[$idfield] == $id) {
                            $resRow = array_merge($subrow, $resRow);
                            $res[$i] = $resRow;
                        }
                    }
                }
            }
        }

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
        
        // Rewrite output, e.g. plugin_output
        // see #2598
        
        $rewrite = $this->getContext()->getModel('Result.OutputRewrite', 'Api', array(
                'target' => $target,
                'optional' => $rd->getParameter('enableRewrite', false)
        ));

        $res = $rewrite->rewrite($res);
        

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
    // make sure cvs are always returned as value->name pairs
    private function fillCustomvariables(array &$columns) {
        $cv_value_field = false;
        $cv_name_field = false;
        foreach($columns as $column) {
            if(preg_match("/CUSTOMVARIABLE_VALUE$/",$column)) {
                $cv_value_field = $column;
            } else if(preg_match("/CUSTOMVARIABLE_NAME$/",$column)) {
                $cv_name_field = $column;
            }
        }
        if($cv_value_field && !$cv_name_field)
            $columns[] = str_replace("VALUE","NAME",$cv_value_field);
        if($cv_name_field && !$cv_value_field)
            $columns[] = str_replace("NAME","VALUE",$cv_name_field);
    }
    
    public function setColumns($search,AgaviRequestDataHolder $rd) {
        if ($search->getSearchType() == IcingaApiConstants::SEARCH_TYPE_COUNT) {
            $search->setResultColumns($rd->getParameter("countColumn"));
            return true;
        }

        $columns = $rd->getParameter("columns",null);
        $columns_default = self::$defaultColumns[$rd->getParameter("target")];

        if(!is_array($columns))
            $columns = array($columns);
        $this->fillCustomvariables($columns);

        // load default columns when nothing is set
        if(is_null($columns) || empty($columns)) {
            $columns = $columns_default;
        }

        $columns_result = array();
        foreach($columns as $column) {
            $column = preg_replace("/[^1-9_A-Za-z]/","",$column);
            $column = strtoupper($column);

            // is the column in our blob field list
            if(in_array($column, $this->blob_columns)) {
                $this->selected_blob_columns[] = $column;
                // add ID column of this target type
                if(isset($columns_default[0])) {
                    if(!in_array($columns_default[0], $columns_result)) {
                        $columns_result[] = $columns_default[0];
                    }
                }
                continue;
            }
            if($column)
                $columns_result[] = $column;
        }

        $search->setResultColumns($columns_result);
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
