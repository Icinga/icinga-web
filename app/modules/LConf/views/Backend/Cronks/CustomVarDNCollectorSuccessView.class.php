<?php

class LConf_Backend_Cronks_CustomVarDNCollectorSuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd)
	{
        if(!$this->getContext()->getUser()->hasCredential('lconf.user'))
            return "{}";
		try {
			$ids = json_decode($rd->getParameter('ids'),true);
			$target = $rd->getParameter('target');
			$target_field = $rd->getParameter('target_field');
			
			$resultSet = $this->fetchDNs();

			$connectionMgr = $this->getContext()->getModel("LDAPConnectionManager","LConf");
			$connections = $connectionMgr->getConnectionsForUser();
			if(!$connections)
				return json_encode(array());
			$arr_result = array();
			foreach($resultSet as $result) {
				if(in_array($result["CUSTOMVARIABLE_OBJECT_ID"],$ids)) {
					// Get the connection descriptor for this dn, if there is none, got to the next object
					$conn = $this->getConnectionsForDN($result["CUSTOMVARIABLE_VALUE"],$connections); 
					if(empty($conn))
						continue;
					$arr_result[$result["CUSTOMVARIABLE_OBJECT_ID"]] = array(
						"DN" => $result["CUSTOMVARIABLE_VALUE"],
						"Connections" => $conn
					);
				}
			}
			return json_encode($arr_result);
			
		} catch(AgaviException $e) {
			$this->getResponse()->setHttpStatusCode("500");
			return json_encode(array("msg"=>$e));
		}
	}
	
	protected function fetchDNs() {
		$api = $this->getContext()->getModel('Icinga.ApiContainer','Web');
		$search = $api->createSearch();				
		$search->setSearchTarget('customvariable');
		$search->setSearchFilter("CUSTOMVARIABLE_NAME","DN");
		$search->setResultType(IcingaApiConstants::RESULT_ARRAY);
		$search->setResultColumns(array("CUSTOMVARIABLE_OBJECT_ID","CUSTOMVARIABLE_NAME","CUSTOMVARIABLE_VALUE"));
	
		return $search->fetch()->getAll();
	}
	
	protected function getConnectionsForDN($dn,array $connections) {
		$foundConnections = array();
		$dn = explode(",",$dn);

		foreach($connections as $connection) {
			$connDN = $connection->getBaseDN();
			if(!$connDN)
				$connDN = $connection->getBindDN();
			$connDN = explode(",",$connDN);

			$break = false;
		/*	for($i=count($connDN)-1,$x = count($dn)-1;$i>=0 && $x>=0;
				$i--,$x--
			) {
				if(!substr($connDN[$i],0,2) == 'dc')
					break;
				if($connDN[$i] != $dn[$x]) {
					$break = true;
					break;
				}
			}				*/
			if(!$break)
				$foundConnections[] = array(
					"id"	=>	$connection->getConnectionId(),
					"name" 	=> 	$connection->getConnectionName()
				);
		}
		return $foundConnections;
	}
}

?>
