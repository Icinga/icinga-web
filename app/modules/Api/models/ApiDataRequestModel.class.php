<?php

class Api_ApiDataRequestModel extends IcingaApiBaseModel 
{


    /**
     * Returns the doctrine connection handler
     * @param String $connName The connection name. Defaults to "icinga" (optional)
     *
     * @return Doctrine_Connection or null
     */
    protected function getDatabaseConnection($connName = "icinga") {
	$db = $this->getContext()->getDatabaseManager()->getDatabase($connName);
	$connection = null;
	if($db)
	    $connection = $db->getConnection();

	return $connection;
    }

    public static function createRequestDescriptor($table) {

    }

    // often used functions are predefined
    public function getServices($filter = null,&$count = false,$offset=0,$limit=0,array $groupFields = array()) {}
    public function getService($serviceName) {}

    public function getHosts($filter = null,&$count = false,$offset=0,$limit=0,array $groupFields = array()) {}
    public function getHost($db,$hostname) {
	$DBALMetaManager = $this->getContext()->getModel("DBALMetaManager","Api");
	$DBALMetaManager->switchIcingaDatabase($db);
		
	$dql = Doctrine_Query::create()->select("*")->from("IcingaHosts h")->leftJoin("h.hostgroups a")->where("a.alias = 'Company 1'");
	$record = $dql->execute(null,Doctrine_Core::HYDRATE_RECORD);
	return $record;
    }

    public function getComments($filter = null,&$count = false,$offset=0,$limit=0,array $groupFields = array()) {}
    public function getCommentById($id) {}
    public function getCommentsForHosts(array $host) {}
    public function getCommentsForServices(array $services) {}

    public function getCustomVariables($filter = null,&$count = false,$offset=0,$limit=0,array $groupFields = array()) {}
    public function getCustomVariablesByName($name) {}
    public function getCustomVariablesForHosts(array $hosts,$name = null) {}
    public function getCustomVariablesForServices(array $hosts,$name = null) {}
    

}

?>
