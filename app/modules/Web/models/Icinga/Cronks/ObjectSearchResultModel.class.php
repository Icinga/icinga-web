<?php

class Web_Icinga_Cronks_ObjectSearchResultModel extends ICINGAWebBaseModel
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
				'object_id'		=> 'HOST_OBJECT_ID'
			)
		),
		
		'service' => array (
			'target'		=> IcingaApi::TARGET_SERVICE,
			'search'		=> 'SERVICE_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'SERVICE_NAME',
				'object_id'		=> 'SERVICE_OBJECT_ID'
			)
		),
		
		'hostgroup' => array (
			'target'		=> IcingaApi::TARGET_HOSTGROUP,
			'search'		=> 'HOSTGROUP_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'HOSTGROUP_NAME',
				'object_id'		=> 'HOSTGROUP_OBJECT_ID'
			)
		),
		
		'servicegroup' => array (
			'target'		=> IcingaApi::TARGET_SERVICEGROUP,
			'search'		=> 'SERVICEGROUP_NAME',
		
			'fields'		=> array (
				'object_name'	=> 'SERVICEGROUP_NAME',
				'object_id'		=> 'SERVICEGROUP_OBJECT_ID'
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
			$search = $fields[ $md[''] ];
			
			$result = $this->api->createSearch()
			->setSearchTarget($md['target'])
			->setResultColumns(array_values($md['fields']))
			->setSearchFilter($md['search'], $this->query, IcingaApi::MATCH_LIKE)
			->setResultType(IcingaApi::RESULT_ARRAY)
			->fetch();
			
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
				$db = strtolower($db);
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