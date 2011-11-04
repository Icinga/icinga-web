<?php

class Api_SLA_SLAQueryBuilderModel extends IcingaApiBaseModel {
    private $connection;
    private $filter; 
    private $AND = false;
    private $query = " WHERE ";
    private $params = array();
    private $serviceGroupJoin = falsE;
    private function addStateFilter() {
        $states = $this->filter->getStates();
        if(!empty($states)) {
            $c = 0;
            foreach($states as $state) { 
                if($c)  $this->query .= " OR ";
                $this->query .= "(state*(scheduled_downtime-1)*-1) = :states_".$c." ";
                $this->params["states_".$c++] = $state;
            }
            $this->AND = true;
        }
    }
    
    private function filterByObjectTypes() {
        if($this->filter->getIncludeHosts() && !$this->filter->getIncludeServices()) {
            if($this->AND)  $this->query .= " AND ";
             $this->query .= "obj.objecttype_id = 1 "; 
            $this->AND = true;
        }
        if($this->filter->getIncludeServices() && !$this->filter->getIncludeHosts()) {
            if($this->AND)  $this->query .= " AND ";
             $this->query .= "obj.objecttype_id = 2 "; 
            $this->AND = true;
        }
    }
    
    private function addNamePatternFilter() {
        
        $hostnamePatterns = $this->filter->getHostnamePattern();
        $servicenamePatterns = $this->filter->getServicenamePattern();
        if(!empty($hostnamePatterns)) {
            if($this->AND)  $this->query .= " AND ";
             $this->query .= "(";
            $c = 0;

            foreach($hostnamePatterns as $p) {
                if($c)
                     $this->query .= " OR ";
                 $this->query .= "obj.name1 LIKE :hostnamepattern_".$c." ";
                $p = str_replace("*","%",$p);
                $this->params["hostnamepattern_".($c++)] = $p;
            }
             $this->query .= ")";
            $this->AND = true;
        }
        
        if(!empty($servicenamePatterns)) {
            if($this->AND) $this->query .= " AND ";
            $this->query  .= "(";
            $c = 0;
            foreach($servicenamePatterns as $p) {
                if($c)
                    $this->query .= " OR ";
                $this->query .= "obj.name1 LIKE :servicenamepattern_".$c." ";
                $p = str_replace("*","%",$p);
                $this->params["servicenamepattern_".($c++)] = $p;
            }
            $this->query .= ")";
            $this->AND = true;
        }
    }
    
    private function addInstanceIdFilter() {
        $instanceIds = $this->filter->getInstanceIds();

        if(!empty($instanceIds)) {
            if($this->AND) $this->query .= " AND ";
            $c = 0;
            $this->query .= "(";

            foreach($instanceIds as $id) {
                if($c)
                    $this->query .= " OR ";
                $this->query .= "obj.instance_id = :instance_id".$c." ";
                $this->params["instance_id".$c++] = $id;
            }
            $this->query .= ") ";

            $this->AND = true;
        }
    }
    
    private function addObjectIdFilter() {
        $objectIds = $this->filter->getObjectIds();

        if(!empty($objectIds)) {
            if($this->AND)  $this->query .= " AND ";
            $c = 0;
             $this->query .= "(";

            foreach($objectIds as $id) {
                if($c)
                     $this->query .= " OR ";
                $this->query .= "s.object_id = :object_id".$c." ";
                $this->params["object_id".$c++] = $id;
            }
            $this->query .= ") ";
            
            $this->AND = true;
        }
    }
    
    private function addCVFilter() {
        $hostCVs = $this->filter->getHostCVs();
        $serviceCVs = $this->filter->getServiceCVs();
        $prefix = $this->connection->getPrefix();
        if(!empty($hostCVs) || !empty($serviceCVs)) {
            $join = " INNER JOIN ".$prefix."customvariables cv ON s.object_id = cv.object_id ";
            if(!empty($hostCVs)) {
                $join = " LEFT JOIN ".$prefix."services srv ON srv.service_object_id = s.object_id ".$join."
                    OR srv.host_object_id = cv.object_id ";
                $this->serviceGroupJoin = true;
            }
            $this->query = $join.$this->query." (";
            $c = 0;
            if(!empty($hostCVs)) {
                foreach($hostCVs as $cv) {
                    if($this->AND)
                         $this->query .= " AND ";
                     $this->query .= "(cv.varvalue = :cv_val".$c." AND cv.varname=:cv_name".$c.")";
                    $this->params["cv_val".$c] = $cv["value"];
                    $this->params["cv_name".$c++] = $cv["name"];
                    
                    $this->AND = true;
                }
            } 
           
            if(!empty($serviceCVs)) {
                foreach($serviceCVs as $cv) {
                    if($this->AND)
                         $this->query .= " AND ";
                    $this->query .= "((cv.varvalue = :cv_val".$c." AND cv.varname = :cv_name".$c.") 
                                    OR obj.objecttype_id != 2)";
                    $this->params["cv_val".$c] = $cv["value"];
                    $this->params["cv_name".$c++] = $cv["name"];
                    $this->AND = true;
                }
            } 
             $this->query .= ")";
        }
    }
    
    public function addGroupFilter() {
        $sg = $this->filter->getServicegroupNames();
        $hg = $this->filter->getHostgroupNames();
        $prefix = $this->connection->getPrefix();
        $isOracle = ($this->connection->getDriverName() == "oracle" || 
                $this->connection->getDriverName() == "icingaOracle");
        $hostGroupIdField = $isOracle ? 'id' : 'hostgroup_id';
        $serviceGroupIdField = $isOracle ? 'id' : 'servicegroup_id';        
        if(empty($sg) && empty($hg))
            return true;

        if(!empty($hg)) {

            $this->query = " INNER JOIN ".$prefix."hostgroup_members hgm ON hgm.host_object_id = s.object_id OR srv.host_object_id = hgm.host_object_id".
            " INNER JOIN  ".$prefix."hostgroups hg ON hg.$hostGroupIdField = hgm.hostgroup_id ".$this->query;

            $c = 0;
            foreach($hg as $hostgroup) {
               if($this->AND)
                   $this->query .= " AND ";
               $this->query .= "hg.alias = :hostgroup$c ";
               $this->params["hostgroup".$c++] = $hostgroup;
               $this->AND = true;
            }
        }
        if(!empty($sg)) {
            $c = 0;
            $this->query = " LEFT JOIN ".$prefix."servicegroup_members sgm ON sgm.service_object_id = s.object_id ".
            " LEFT JOIN  ".$prefix."servicegroups sg ON sg.$serviceGroupIdField = sgm.servicegroup_id ".$this->query;

            
            foreach($sg as $servicegroup) {
               if($this->AND)
                   $this->query .= " AND ";
               $this->query .= "(sg.alias = :servicegroup$c OR obj.objecttype_id = 1)";
               $this->params["servicegroup".$c++] = $servicegroup;
               $this->AND = true;
            }
        }
        if(!$this->serviceGroupJoin)
            $this->query = " LEFT JOIN ".$prefix."services srv ON srv.service_object_id = s.object_id ".$this->query;
            $this->serviceGroupJoin = true;
    }
    
    public function getWherePart(Doctrine_Connection $conn,Api_SLA_SLAFilterModel $filter) {
        $this->connection = $conn;
        $this->filter = $filter;
      
        $this->addStateFilter();
        $this->filterByObjectTypes();
        $this->addNamePatternFilter();
        $this->addInstanceIdFilter();
        $this->addObjectIdFilter();
        $this->addCVFilter();
        $this->addGroupFilter();

        if(!$this->AND)
            return array("wherePart" => "", "params" => array()); // no filter

        return array("wherePart" => $this->query, "params" => $this->params);
    }
}