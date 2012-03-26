<?php

class LConf_LDAPConnectionManagerModel extends IcingaLConfBaseModel 
{
	protected $connectionArray = array();
	protected $scope = array();
	
	private $allModels = null;
	
	public function addToConnectionArray(LConf_LDAPConnectionModel $conn) {
		$this->connectionArray[$conn->getConnectionId()] = $conn;
	}	
	
	public function removeFromConnectionArray($conn) {
		$connectionArray = &$this->getConnectionArray();
		$idToRemove = null;
		
		// check whether an object to remove is given or just an id
		// in both cases, we want to end with the id in idToRemove
		if($conn instanceof LConf_LDAPConnectionModel) {
			if(array_key_exists($conn->getConnectionId(),$connectionArray))
				$idToRemove = $conn->getConnectionId();
		} else {
			if(array_key_exists($conn,$connectionArray))
				$idToRemove = $conn;
		}
		
		// if entry doesn't exist, return with false;
		if(!$idToRemove)
			return false;	
		unset($connectionArray[$idToRemove]);
	}
	
	public function getConnectionArray() {
	
		return $this->connectionArray;
	}
	
	public function getConnectionById($nr) {
		$connections = $this->getConnectionArray();
		if(array_key_exists($nr,$connections))			
			return $connections[$nr];
		else 
			return null;
	}
	
	public function getScope()	{
		return $this->scope;
	}
	
	public function setConnectionArray(array $connectionArray) {
		$this->connectionArray = $connectionArray;
	}
	
	/**
	 * Updates or adds an connection
	 * @param array $details
	 */
	public function addConnection(array $details) {
		$id = $details["connection_id"];
		$alwaysUpdate = array("connection_binddn","connection_ldaps","connection_tls");
		$entry = new LconfConnection();
		if($id > -1) 
			$entry = Doctrine::getTable("LconfConnection")->findBy("connection_id",$id)->getFirst();
		else 
			$details["connection_id"] = null;
		if(!$entry)
			throw new AppKitException("Connection not found!");
		foreach($details as $field=>$value) {
			if(!ctype_digit($value))
			    $value = htmlentities($value);
			if(($field == "connection_ldaps" || $field == "connection_tls") && $value == "")
				$value = 0;
			if($value  || in_array($field,$alwaysUpdate))
				$entry->set($field,$value);
		}
		$user = $this->getContext()->getUser()->getNsmUser();
		if($details["connection_id"] == null)
			$entry->set("owner",$user->get('user_id'));	
		if(!$this->isOwner($entry))
			throw new AppKitException("Not allowed to modify this connection");
		$entry->save();
		return $entry->getIncremented();	
	}
	
	protected function isOwner(LconfConnection $l) {
		$user = $this->getContext()->getUser()->getNsmUser();
		if(!$this->getContext()->getUser()->hasCredentials("lconf.admin")) {
			if($l->get("owner") != $user->get('user_id'))
				return 	false;
		}
		return true;
	}	
	
	/**
	 * Deletes an connection
	 * @param integer $id
	 */
	public function dropConnection($id) {
		$entry = new LconfConnection();
		$entry = Doctrine::getTable("LconfConnection")->findBy("connection_id",$id)->getFirst();
		if(!$entry)
			throw new AppKitException("Connection does not exist!");
		$user = $this->getContext()->getUser()->getNsmUser();
		if(!$this->isOwner($entry))
			throw new AppKitException("Not allowed to drop this connection");
		
		$entry->delete();
	}
	
	public function addScope($scope)	{
		$this->setConnectionArray(array());
		$this->scope[] = $scope;
	}
	public function setScope(array $scope) {
		$this->setConnectionArray(array());
		$this->scope = $scope;
	}
	/*
	public function markDefaultConnection() {
		$user = $this->getContext()->getUser()->getNsmUser();
		
		$result = Doctrine_Query::create()
				 ->select("def.connection_id")
				 ->from("LconfDefaultconnection def")
				 ->where("def.user_id = ".$user->get("user_id"))
				 ->execute()->toArray();

		$this->getConnectionById($result[0]["connection_id"])->setDefault(true);
	
	}
	*/
	public function getConnectionsFromDB() {
		$connections = Doctrine_Query::create()->
							select("lc.*, def.*")	
							->from("LconfConnection lc")->fetchArray();
		$ctx = $this->getContext();
		foreach($connections as $connection) {
			$this->addToConnectionArray(
				$ctx->getModel("LDAPConnection","LConf",array($connection))
			);	
		}
//		$this->markDefaultConnection();
		return $connections;
	}

	public function userIsGranted($connectionId) {
		$this->connectionArray = array();
		$connections = $this->getConnectionsForUser();
		
		if(!array_key_exists($connectionId,$this->connectionArray))
			return false;
		$this->connectionArray = array();
		return true;
	}
	
	
	public function __toJSON() {
		$arr = array();

		foreach($this->getConnectionArray() as $connection) {	
			if($connection instanceof LConf_LDAPConnectionModel)
				$arr[] = $connection->__toArray();
				
		}
		return json_encode(array("success"=>true,"connections" => $arr));
	}


	public function getConnectionsForUser(NsmUser $user = null,$respectGroups = true) {
		if(is_null($user))
			$user = $this->getContext()->getUser()->getNsmUser();
		
		$ctx = $this->getContext();
		$query = Doctrine_Query::create()
				->select("conn.*, def.user_id")
				->from("LconfConnection conn")
				->innerJoin("conn.principals lp")
		//		->leftJoin("conn.default def WITH def.user_id = ".$user->get("user_id"))				
				->where("lp.principal_user_id = ?",$user->get("user_id"))
				->orWhere('conn.owner = ?',$user->get("user_id"));

		
		$connections = $query->execute()->toArray();

		$result = array();
		foreach($connections as $connection) {
			$this->addToConnectionArray($ctx->getModel("LDAPConnection","LConf",array($connection)));
		}
		
		if($respectGroups) {
			foreach($user->get("NsmUserRole") as $role) {
				$groupConnections = $this->getConnectionsForGroup($role->get("NsmRole"));
			}
		}
		//$this->markDefaultConnection();
		return $this->getConnectionArray();
	}
	
	
	public function getConnectionsForGroup(NsmRole $role) {
		$ctx = $this->getContext();
		$query = Doctrine_Query::create()
				->select("conn.*")
				->from("LconfConnection conn")
				->innerJoin("conn.principals lp")
				->where("lp.principal_role_id = ?",$role->get("role_id"));
		return $this->processQuery($query);
	}
	

	public function getUsersForConnection($connection) {
		if($connection instanceof LConf_LDAPConnectionModel)
			$connection = $connection->getConnectionId();
		$ctx = $this->getContext();
		$query = Doctrine_Query::create()
				->select("user.user_id, user.user_name, pr.*")
				->from("LconfPrincipal pr")
				->innerJoin("pr.NsmUser user")
				->where("pr.connection_id = ?",$connection);
		
		$result = $query->execute()->toArray();
		foreach($result as &$entry) {
			$entry["NsmUser"]["principal_id"] = $entry["principal_id"];
			$entry = $entry["NsmUser"];
		}
		return $result;
	}

	public function getGroupsForConnection($connection) {
		if($connection instanceof LConf_LDAPConnectionModel)
			$connection = $connection->getConnectionId();
		$ctx = $this->getContext();
		$query = Doctrine_Query::create()
				->select("role.role_name, role.role_id, pr.connection_id")
				->from("LconfPrincipal pr")
				->innerJoin("pr.NsmRole role")
				->where("pr.connection_id = ?",$connection);
		$result = $query->execute()->toArray();
		foreach($result as &$entry) {
			$entry["NsmRole"]["principal_id"] = $entry["principal_id"];
			$entry = $entry["NsmRole"];
		}
		return $result;
	}

	protected function processQuery($query) {
		$ctx = $this->getContext();
		$connections = $query->execute()->toArray();
		$result = array();
		foreach($connections as $connection) {
			$result[] = $ctx->getModel("LDAPConnection","LConf",array($connection));
		}
		foreach($result as $curResult)
			$this->addToConnectionArray($curResult);
		return $result;			
	}
	
}

?>
