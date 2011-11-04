<?php

class Api_SLA_SLAFilterModel extends IcingaApiBaseModel {

    private $startTime = null;
    private $endTime = null;
    private $ignoreAcknowledged = false;
    private $ignoreDowntimes = false;
    private $objectIds = array();
    private $hostnamePatterns = array();
    private $servicenamePatterns = array();
    private $instanceIds = array();
    private $states = array();
    private $includeHosts = true;
    private $includeServices = true;
    private $servicegroupNames = array();
    private $hostgroupNames = array();
    private $limitToContactgroup = false;
    private $hostCustomVariables = array();
    private $serviceCustomVariables = array();
    
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        //$this->endTime = time();
        if(!empty($parameters))
            $this->__fromArray($parameters);
    }
    
    
    public function getStartTime() {
        if(!$this->startTime)
            return null;
        else 
            return Date("Y-m-d H:i:s",$this->startTime);
        
    }
    public function getEndTime() {
        if($this->endTime == time() || !$this->endTime)
            return null;

        return Date("Y-m-d H:i:s",$this->endTime);
    }
    public function getIgnoreAcknowledged() {
        return $this->ignoreAcknowledged;
    }
    public function getIgnoreDowntimes() {
        return $this->ignoreDowntimes;
    }
    public function getHostnamePattern() {
        return $this->hostnamePatterns;
    }
    public function getServicenamePattern() {
        return $this->servicenamePatterns;
    }
    public function getObjectIds() {
        return $this->objectIds;
    }
    public function getInstanceIds() {
        return $this->instanceIds;
    }

    public function getStates() {
        return $this->states;
    }
    public function getIncludeHosts() {
        return $this->includeHosts;
    }
    public function getIncludeServices() {
        return $this->includeServices;
    }
    public function getServicegroupNames() {
        return $this->servicegroupNames;
    }
    public function getHostgroupNames() {
        return $this->hostgroupNames;
    }    
    public function getHostCVs() {
        return $this->hostCustomVariables;
    }
    public function getServiceCVs() {
        return $this->serviceCustomVariables;
    }
    public function onlyContacts() {
        return $this->limitToContactgroup;
    }
    
    public function setTimespan($timespan,$fromStart = false) {
        $offset = $this->endTime ? $this->endTime : time();
        if($fromStart) {
            if($this->startTime == null)
                $this->setStartTime(time());
            $time = strtotime($timespan, $this->startTime);
            if(!$time)
                $this->getContext()->getLoggerManager()
                    ->log("Invalid timespan (offset from start) given to SLA Filter, using time() as enddate");
            $this->setEndTime($time ? $time : time());
            
        } else { 
            $time = strtotime($timespan, $offset);
            if(!$time)
                $this->getContext()->getLoggerManager()
                    ->log("Invalid timespan given to SLA Filter, using time() as startdate");
            $this->setStartTime($time ? $time : time());
        }
    }
    public function setStartTime($date) {
        $this->startTime = $date;
    }
    public function setEndTime($date) {
        $this->endTime = $date;
    }
    public function setIgnoreAcknowledge($boolean) {
        $this->ignoreAcknowledged = $boolean !== false;
    }    
    public function setIgnoreDowntimes($boolean) {
        $this->ignoreDowntime = $boolean !== false;
    }    
    public function setHostnamePattern($pattern) {
        $this->hostnamePatterns = is_array($pattern) ? $pattern : array($pattern);
    }
    public function setServicenamePattern($pattern) {
        $this->servicenamePatterns = is_array($pattern) ? $pattern : array($pattern);
    }
    public function setObjectId($ids) {
        $this->objectIds = is_array($ids) ? $ids : array($ids);
    }
    public function setHostgroupNames($names) {
        $this->hostgroupNames = is_array($names) ? array_values($names) : array($names);
    }
    public function setServicegroupNames($names) {
        $this->servicegroupNames = is_array($names) ? array_values($names) : array($names);
    }
    public function setInstanceIds($ids) {
        $this->instanceIds = is_array($ids) ? $ids : array($ids);
    }
    public function setStates($states) {
        $this->states = is_array($states) ? $states : array($states);
    }
    public function useOnlyHosts() {
        $this->includeHosts = true;
        $this->includeServices = false;
    }
    public function useOnlyServices() {
        $this->includeHosts = false;
        $this->includeServices = true;
    }
    public function useHostsAndServices() {
        $this->includeHosts = true;
        $this->includeServices = true;
    }
    public function setOnlyContacts($bool) {
        $this->limitToContactgroup = $bool;
    }
    
    public function addHostCV($name,$value) {
     
        $this->hostCustomVariables[] =array("name"=>$name, "value" => $value);
               
    }
    public function addServiceCV($name,$value) {
        $this->serviceCustomVariables[] =array("name"=>$name, "value" => $value);
        
    }

    
    private function __fromArray($array) {
        if(isset($array["startTime"]))
            $this->setStartTime($array["startTime"]);
        if(isset($array["endTime"]))
            $this->setEndTime($array["endTime"]);
        if(isset($array["timespan"])) 
            $this->setTimespan($array["timespan"]);
        if(isset($array["ignoreAcknowledged"]))
            $this->setIgnoreAcknowledged($array["ignoreAcknowledged"]);
        if(isset($array["ignoreDowntimes"]))
            $this->setIgnoreDowntimes($array["ignoreDowntimes"]);
        if(isset($array["hostnames"]))
            $this->setHostnamePattern($array["hostnames"]);
        if(isset($array["servicenames"]))
            $this->setServicenamePattern($array["servicenames"]);
        if(isset($array["ids"]))
            $this->setObjectId($array["ids"]);
        if(isset($array["hostgroupIds"]))
            $this->setHostgroupIds($array["hostgroupIds"]);
        if(isset($array["servicegroupIds"]))
            $this->setServicegroupIds($array["servicegroupIds"]);
        if(isset($array["instanceIds"]))
            $this->setInstanceIds($array["instanceIds"]);
        if(isset($array["states"]))
            $this->setStates($array["states"]);
        if(isset($array["hostsOnly"]))
            $this->useOnlyHosts();
        if(isset($array["servicesOnly"]))
            $this->useOnlyServices();
    }
    
   
}

?>
