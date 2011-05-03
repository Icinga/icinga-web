<?php

class Cronks_System_ObjectSearchResultModel extends CronksBaseModel {
	
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
	
	private $mapping_fields = array (
		'object_name', 'object_name2', 
		'object_id', 'description', 'data1',
		'object_status'
	);
	
	/**
	 * The mapping array
	 * @var array
	 */
	
	private $mapping = array (
		'host'	=> array (
			'target'		=> IcingaApi::TARGET_HOST,
			'search'		=> array('HOST_NAME', 'HOST_ALIAS'),
		
			'fields'		=> array (
				'object_name'	=> 'HOST_NAME',
				'object_id'		=> 'HOST_OBJECT_ID',
				'description'	=> 'HOST_ALIAS',
				'data1'			=> 'HOST_ADDRESS',
				'object_status'	=> 'HOST_CURRENT_STATE'
			)
		),
		
		'service' => array (
			'target'		=> IcingaApi::TARGET_SERVICE,
			'search'		=> array('SERVICE_NAME', 'SERVICE_DISPLAY_NAME'),
		
			'fields'		=> array (
				'object_name'	=> 'SERVICE_NAME',
				'object_id'		=> 'SERVICE_OBJECT_ID',
				'object_name2'	=> 'HOST_NAME',
				'description'	=> 'SERVICE_DISPLAY_NAME',
				'object_status'	=> 'SERVICE_CURRENT_STATE'
		
			)
		),
		
		'hostgroup' => array (
			'target'		=> IcingaApi::TARGET_HOSTGROUP,
			'search'		=> array('HOSTGROUP_NAME', 'HOSTGROUP_ALIAS'),
		
			'fields'		=> array (
				'object_name'	=> 'HOSTGROUP_NAME',
				'object_id'		=> 'HOSTGROUP_OBJECT_ID',
				'description'	=> 'HOSTGROUP_ALIAS'
			)
		),
		
		'servicegroup' => array (
			'target'		=> IcingaApi::TARGET_SERVICEGROUP,
			'search'		=> array('SERVICEGROUP_NAME', 'SERVICEGROUP_ALIAS'),
		
			'fields'		=> array (
				'object_name'	=> 'SERVICEGROUP_NAME',
				'object_id'		=> 'SERVICEGROUP_OBJECT_ID',
				'description'	=> 'SERVICEGROUP_ALIAS'
			)
		),
	);
	
	public function initialize (AgaviContext $c, array $p=array()) {
		parent::initialize($c, $p);
		$this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web')->getConnection();
	}

	/**
	 * Injects the search query from
	 * input source
	 * @param string $query
	 * @return boolean always true
	 */
	public function setQuery($query) {
		
		if (strpos($query, '*') !==false) {
			$query = str_replace('*', '%', $query);
		}
		
		// Makes it easier for the user
		if (strpos($query, '%') == false) {
			$query .= '%';
		}
		
		$this->query = $query;

		return true;
	}
	
	public function setSearchType($type) {
		$this->type = $type;
	}
	
	public function getData() {
		return $this->bulkQuery();
	}
	
	private function bulkQuery() {
		
		// We want only one specific type
		if ($this->type && array_key_exists($this->type, $this->mapping)) {
			$mappings = array($this->type);
			
		}
		else {
			$mappings = array_keys($this->mapping);
		}
		
		$data = array();
		$count = array();
		
		foreach ($mappings as $mapping) {
			$md = $this->mapping[$mapping];
			$fields = $md['fields'];
			
			$search = $this->api->createSearch()
			->setSearchTarget($md['target'])
			->setResultColumns(array_values($md['fields']))
			->setResultType(IcingaApi::RESULT_ARRAY)
			->setSearchLimit(0, AgaviConfig::get('modules.cronks.search.maximumResults', 200));
			
			$search_group = $search->createFilterGroup(IcingaApi::SEARCH_OR);
			
			foreach ($md['search'] as $search_field) {
				$search_group->addFilter($search->createFilter($search_field, $this->query, IcingaApi::MATCH_LIKE));
			}
			
			$search->setSearchFilter($search_group);
			
			// Limiting results for security
			IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);
			
			$result = $search->fetch();

			$count[$mapping] = $result->getResultCount();
			$data[$mapping] = $this->resultToArray($result, $fields, $mapping);
		}
		
		$new = $this->sortArray($data, $count);
		$sum = array_sum($count);
		
		return array (
			'resultCount'		=> array_sum($count),
			'resultRows'		=> $new,
			'resultSuccess'		=> ($sum>0) ? true : false
		);
	}

	/**
	 * Well do not know what christian
	 * was doing here ;-)
	 * @param array $add
	 * @param array $out
	 */
	private function addToArray(array $add, array &$out) {
		foreach ($add as $suba) {
			$out[] = $suba;
		}
	}
	
	private function sortArray(array $d, array $c) {
		
		$out = array ();
		$data = array ();
		$out = array ();
		
		arsort($c);
		
		foreach ($c as $key=>$count) {
			
			$sort_oname = array ();
			$sort_oname2 = array ();
			foreach ($d[$key] as $rid=>$row) {
				$sort_oname[$rid] = $row['object_name'];
				$sort_oname2[$rid] = $row['object_name2'];
			}
			
			array_multisort($sort_oname, SORT_ASC, $sort_oname2, SORT_ASC, $d[$key]);
			$this->addToArray($d[$key], $out);
		}
		
		return $out;
	}
	
	private function resultToArray(IcingaApiResult &$res, array $fieldDef, $type) {
		$array = array ();

		foreach ($res as $oRow) {
			$row = $oRow->getRow();
			$tmp = array ('type' => $type);
			foreach ($this->mapping_fields as $field) {
				$tmp[$field] = null;
				if (array_key_exists($field, $fieldDef) && array_key_exists($fieldDef[$field], $row)) {
					$tmp[$field] = $row[$fieldDef[$field]];
				}
			}
			$array[] = $tmp;
		}
		return $array;
	}
	
}

?>
