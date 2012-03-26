<?php



class LConf_LDAPConnectionModel extends IcingaLConfBaseModel
{
	protected $id;
	protected $connectionName;
	protected $connectionDescription;
	protected $bindDN;
	protected $bindPass;
	protected $baseDN;
	protected $host;
	protected $port;
	protected $ownerid;
	protected $default = false;
	protected $authType = "simple";
	protected $TLS = false;
	protected $ldaps = false;
	
	// Getter and Setter
	
	static public $supportedAuthTypes = array("none","simple","sasl");
	
	public function getConnectionId()	{
		return $this->id;
	}
	
	public function getConnectionName() {
		return $this->connectionName;
	}
	
	public function getConnectionDescription() {
		return $this->connectionDescription;
	}
	
	public function getBindDN()	{
		return $this->bindDN;
	}
	
	public function getBindPass()  {
		return $this->bindPass;
	}
	
	public function getBaseDN() {
		return $this->baseDN;
	}
	
	public function getHost()	{
		return $this->host;
	}
	
	public function getOwnerId() {
		return $this->ownerid;
	}

	public function getPort()	{
		return $this->port;
	}
	
	public function isDefault() {
		return $this->default;
	}
	
	public function isOwner() {
		if($this->getContext()->getUser()->hasCredentials("lconf.admin"))
			return true;
		$id = $this->getContext()->getUser()->getNsmUser()->get("user_id");
		return $this->getOwnerId() == $id;
	}

	public function getAuthType()	{
		return $this->authType;
	}
	
	public function usesTLS()	{
		return $this->TLS;
	}
	
	public function isLDAPS()	{
		return $this->ldaps;
	}
	
	public function setConnectionId($id) {
		$this->id = $id;
	}
	public function setConnectionName($connName) {
		$this->connectionName = $connName;
	}
	
	public function setConnectionDescription($desc) {
		$this->connectionDescription = $desc;
	}
	
	public function setBindDN($dn)	{
		$this->bindDN = $dn;
	}
		
	public function setBindPass($pass) {
		$this->bindPass = $pass;
	}
	
	public function setBaseDN($dn)	{
		$this->baseDN = $dn;
	}
	
	public function setHost($host)	{
		$this->host = $host;
	}
	
	public function setPort($port)	{
		$this->port = $port;
	}

	public function setDefault($bool) {
		$this->default = (boolean) $bool;
	}

	public function setOwnerId($id) {
		$this->ownerid = $id;
	}	
	
	public function setAuthType($authType) {
		if(in_array($authType,self::$supportedAuthTypes)) {
			$this->authType = $authType;
		} else {
			throw new AgaviException("Authtype ".$authType." is currently not supported for lconf.");
		}
	}
	
	public function setTLS($bool) {
		$this->TLS = (boolean) $bool;
	}
	
	public function setLDAPS($bool) {
		$this->ldaps = (boolean) $bool;
	}
	
	public function __construct(array $parameter = null) {
		// Parse parameter if exist
		//print_r($parameter);
		
		if(!empty($parameter)) {
			if(isset($parameter["connection_id"]))
				$this->setConnectionId($parameter["connection_id"]);
			if(isset($parameter["connection_name"]))
				$this->setConnectionName($parameter["connection_name"]);
			if(isset($parameter["connection_description"]))
				$this->setConnectionDescription($parameter["connection_description"]);
			if(isset($parameter["connection_binddn"]))
				$this->setBindDN($parameter["connection_binddn"]);
			if(isset($parameter["connection_bindpass"]))
				$this->setBindPass($parameter["connection_bindpass"]);
			if(isset($parameter["connection_host"]))
				$this->setHost($parameter["connection_host"]);
			if(isset($parameter["connection_port"]))
				$this->setPort($parameter["connection_port"]);
			if(isset($parameter["default"]))
				$this->setDefault($parameter["default"]);
			if(isset($parameter["connection_basedn"]))
				$this->setBaseDN($parameter["connection_basedn"]);
			if(isset($parameter["connection_tls"]))
				$this->setTLS($parameter["connection_tls"]);	
			if(isset($parameter["connection_ldaps"]))
				$this->setLDAPS($parameter["connection_ldaps"]);	
			if(isset($parameter["owner"])) {
				$this->setOwnerId($parameter["owner"]);
			}
		}
		
	}
	
	public function __toString() {
		return "LDAP_Connection_".$this->getConnectionId();
	}
	
	public function __toArray($wPass = false) {
		$arr = array(
			"connection_id" => $this->getConnectionId(),
			"connection_name" => $this->getConnectionName(),
			"connection_description" => $this->getConnectionDescription(), 
			"connection_binddn" => $this->getBindDN(),
			"connection_bindpass" => $wPass ? $this->getBindPass() : '',
			"connection_basedn" => $this->getBaseDN(),
			"connection_host" => $this->getHost(),
			"connection_port" => $this->getPort(),
			"connection_default" => $this->isDefault(),
	//		"authType" =>$this->getAuthType(),
			"connection_tls" => $this->usesTLS(),
			"connection_ldaps" => $this->isLDAPS(),
			"owner" => $this->getOwnerId(),
			"is_owner" => $this->isOwner()
		);
		return $arr;
	}
}



?>
