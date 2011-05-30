<?php
/**
 * Class that encapsulates helpers for the ApiDataRequestModel
 * All methods must return a IcingaApiRequestDescriptor
 */
class IcingaApiRequestHelper {


    public function getService($serviceName,$hostName,$instance = null) {}
    public function getServicesInServiceGroup($servicegroup,$instance = null,&$count = null,$offset = 0,$limit = -1) {}
    public function getServicesForHost($host,$instance = null,&$count = null,$offset = 0,$limit = -1) {}
    public function getHistoryForServices(array $services,$instance = null,&$count = null,$offset = 0,$limit = -1);

    public function getHostsInHostGroup($hostgroup,$instance = null,&$count = null,$offset = 0,$limit = -1) {}
    public function getHost($hostName,$instance = null) {}
    public function getHistoryForHosts(array $hostNames,$instance = null,&$count = null,$offset = 0,$limit = -1) {}

    public function getCommentById($id) {}
    public function getCommentsForHosts(array $host,$instance = null,&$count = null,$offset = 0,$limit = -1) {}
    public function getCommentsForServices(array $services,$instance = null,&$count = null,$offset = 0,$limit = -1) {}

    public function getCustomVariablesByName($name,$instance = null,&$count = null,$offset = 0,$limit = -1) {}
    public function getCustomVariablesForHosts(array $hosts,$name = null,$instance = null,&$count = null,$offset = 0,$limit = -1) {}
    public function getCustomVariablesForServices(array $services,$name = null,$instance = null,&$count = null,$offset = 0,$limit = -1) {}

    public function getHostgroup($name,$instance = null) {}
    public function getServicegroup($name,$instance = null) {}

    public function getInstance($name) {}


}

?>