<?php

class Cronks_System_ObjectSearchResultModel extends ICINGACronksBaseModel
{

	/**
	 * 
	 * @var IcingaApiConnectionIdo
	 */
	private $api = null;
	
	/**
	 * Our query
	 * @var string
	 */
	private $query = null;
	
	/**
	 * A searchtype
	 * @var string
	 */
	private $type = null;
	
	/**
	 * The mapping array
	 * @var array
	 */
	
	private $mapping = array (
		'host'	=> array (
			'target'		=> IcingaApi::TARGET_HOST,
			'search'		=> 'HOST_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'HOST_NAME',
				'object_id'		=> 'HOST_OBJECT_ID',
				'description'	=> 'HOST_ALIAS',
	
				'data1'			=> 'HOST_ADDRESS'
			),
			
			'security'		=> array(
				'IcingaHostgroup',
				'IcingaCustomVariablePair',
				'IcingaContactgroup'
			)
		),
		
		'service' => array (
			'target'		=> IcingaApi::TARGET_SERVICE,
			'search'		=> 'SERVICE_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'SERVICE_NAME',
				'object_id'		=> 'SERVICE_OBJECT_ID',
				'object_name2'	=> 'HOST_NAME',
				'description'	=> 'SERVICE_DISPLAY_NAME'
			),
			
			'security'		=> array(
				'IcingaHostgroup',
				'IcingaServicegroup',
				'IcingaCustomVariablePair',
				'IcingaContactgroup'
			)
		),
		
		'hostgroup' => array (
			'target'		=> IcingaApi::TARGET_HOSTGROUP,
			'search'		=> 'HOSTGROUP_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'HOSTGROUP_NAME',
				'object_id'		=> 'HOSTGROUP_OBJECT_ID',
				'description'	=> 'HOSTGROUP_ALIAS'
			),
			
			'security'		=> array(
				'IcingaHostgroup'
			)
		),
		
		'servicegroup' => array (
			'target'		=> IcingaApi::TARGET_SERVICEGROUP,
			'search'		=> 'SERVICEGROUP_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'SERVICEGROUP_NAME',
				'object_id'		=> 'SERVICEGROUP_OBJECT_ID',
				'description'	=> 'SERVICEGROUP_ALIAS'
			),
			
			'security'		=> array(
				'IcingaServicegroup'
			)
		),
	);
	
	public function __construct() {
		$this->api = $this->api = AppKitFactories::getInstance()->getFactory('IcingaData')->API();
	}
	
	public function setQuery($query) {
		
		// Append search suffix
		if (strpos($query, '%') == false) {
			$query .= '%';
		}
		
		$this->query = $query;
	}
	
	public function setSearchType($type) {
		$this->type = $type;
	}
	
	public function getData() {
		return $this->bulkQuery();
	}
	
	private function bulkQuery() {
		
		$data = array ();
		
		// We want only one specific type
		if ($this->type && array_key_exists($this->type, $this->mapping)) {
			$mappings = array($this->type);
			
		}
		else {
			$mappings = array_keys($this->mapping);
		}
		
		foreach ($mappings as $mapping) {
			$md = $this->mapping[$mapping];
			$fields = $md['fields'];
			$security = (isset($md['security']) && is_array($md['security'])) ? $md['security'] : array();
			
			$search = $this->api->createSearch()
			->setSearchTarget($md['target'])
			->setResultColumns(array_values($md['fields']))
			->setSearchFilter($md['search'], $this->query, IcingaApi::MATCH_LIKE)
			->setResultType(IcingaApi::RESULT_ARRAY);
			
			// Limiting results for security
			IcingaPrincipalTargetTool::applyApiSecurityPrincipals($security, $search);
			
			$result = $search->fetch();
			
			$data[ $mapping ] = array (
				'resultSuccess'		=> true,
				'resultCount'		=> $result->getResultCount(),
				'resultRows'		=> $this->resultToArray($result, $fields)
			);
		}
		
		return $data;
	}
	
	private function resultToArray(IcingaApiResult &$res, array $fieldDef) {
		$out = array ();
		foreach ($res as $oRow) {
			$row = $oRow->getRow();
			$tmp = array ();
			foreach ($fieldDef as $name=>$db) {
				$db = strtoupper($db);
				if (array_key_exists($db, $row)) {
					$tmp[$name] = $row[$db];
				}
			}
			
			$out[] = $tmp;
		}
		return $out;
	}
	
}

?>