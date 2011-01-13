<?php
class IcingaDoctrine_Query extends Doctrine_Query {
	public static function create($conn = NULL, $class = NULL) {
		return new IcingaDoctrine_Query($conn);
	}
	
	public function execute($attr,$hyd) {
		ApiDataRequestBaseModel::applySecurityPrincipals($this);
		return parent::execute($attr,$hyd);
	}
}


class ApiDataRequestBaseModel extends IcingaApiBaseModel 
{
    protected $database = "icinga";
    public function getDatabase() {
	return $this->database;
    }
    public function setDatabase($dbname) {
	$this->database = $dbname;
    }

    public static function applySecurityPrincipals(Doctrine_Query $q) {}
    /**
     * Returns the doctrine connection handler
     * @param String $connName The connection name. Defaults to "icinga" (optional)
     *
     * @return Doctrine_Connection or null
     */
    protected function getDatabaseConnection($connName = NULL) {
	if(!$connName)
		$connName = $this->database;
	$db = $this->getContext()->getDatabaseManager()->getDatabase($connName);
	$connection = null;
	if($db)
	    $connection = $db->getConnection();

	return $connection;
    }

    public function createRequestDescriptor($connName = NULL) {
	if(!$connName)
		$connName = $this->database;
	$DBALMetaManager = $this->getContext()->getModel("DBALMetaManager","Api");
	$DBALMetaManager->switchIcingaDatabase($connName);	
	return IcingaDoctrine_Query::create();
    }
   

}

?>
