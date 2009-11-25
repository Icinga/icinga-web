<?php 

class IcingaTemplateWorker {
	
	/**
	 * @var IcingaTemplateXmlParser
	 */
	private $template		= null;
	
	/**
	 * @var IcingaApiConnectionIdo
	 */
	private $api			= null;
	
	/**
	 * @var IcingaApiSearchIdo
	 */
	private $api_search		= null;
	
	/**
	 * @var IcingaApiSearchIdo
	 */
	private $api_count		= null;
	
	/**
	 * Calculated number of results
	 * @var integer
	 */
	private $result_count	= null;
	
	/**
	 * The size of a page
	 * @var integer
	 */
	private $pager_limit	= null;
	
	/**
	 * Where the page starts
	 * @var integer
	 */
	private $pager_start	= null;
	
	private $sort_orders	= array();
	
	private $conditions		= array();
	
	public function __construct(IcingaTemplateXmlParser &$template = null) {
		if ($template) $this->setTemplate($template);
	}
	
	public function setTemplate(IcingaTemplateXmlParser &$template) {
		$this->template =& $template;
	}
	
	public function setApi(IcingaApi &$api) {
		$this->api =& $api;
	}
	
	/**
	 * @return IcingaTemplateXmlParser
	 */
	public function getTemplate() {
		return $this->template;
	}
	
	/**
	 * Returns the icinga data api
	 * @return IcingaApiConnectionIdo
	 */
	public function getApi() {
		return $this->api;
	}
	
	public function buildAll() {
		$this->buildDataSource();
	}
	
	public function fetchDataArray() {
		return $this->getDataAsArray();
	}
	
	/**
	 * Return the number of result rows.
	 * @return integer
	 */
	public function countResults() {
		
		$params = $this->getTemplate()->getSectionParams('datasource');
		
		if ($params->getParameter('countmode', null) !== 'simple') {
			if ($this->api_count !== null) {
				$this->api_count->setSearchType(IcingaApi::SEARCH_TYPE_COUNT);
				
				if (is_array(($fields = $params->getParameter('countfields'))) && count($fields)) {
					$this->api_count->setResultColumns($fields);
					$this->api_count->setResultType(IcingaApi::RESULT_ARRAY);
					$result  = $this->api_count->fetch();
					
					// Try to determine the fields
					$row = $result->getRow();
					$fields = array_keys($row);
					$field = array_shift($fields);
					
					
					$this->result_count = (int)$row[ $field ];
				}
			}
			
			
		}
		
		return $this->result_count;
	}
	
	public function setResultLimit($start, $limit) {
		$this->pager_limit = $limit;
		$this->pager_start = $start;
		return true;
	}
	
	public function setOrderColumn($column, $direction = 'ASC') {
		$this->sort_orders = array();
		return $this->addOrderColumn($column, $direction);
	}
	
	public function addOrderColumn($column, $direction = 'ASC') {
		if ($this->getApiField($column)) {
			$this->sort_orders[] = array($column, $direction);
			return true;
		}
		
		
		return false;
		
	}
	
	private function getDataAsArray() {
		if ($this->api_search !== null) {
			$data = array ();
			
			foreach ($this->api_search->fetch() as $result) {
				
				if ($this->result_count === null) {
					$this->result_count = $result->getResultCount();
				}
				
				$tmp = $this->rewriteResultRow($result);
				
				/*
				 * @todo add additional fields and content here
				 */
				
				$data[] = $tmp;
			}
			
			return $data;
		}
	}
	
	/**
	 * 
	 * @param IcingaApiResult$result
	 * @return ArrayObject
	 */
	private function addAdditionalFieldResults(IcingaApiResult $result) {
		$out = new ArrayObject();
		$ds = $this->getTemplate()->getSection('datasource');
		
		if (isset($ds['additional_fields']) && is_array($ds['additional_fields'])) {
			$row = new ArrayObject($result->getRow());

			foreach ($ds['additional_fields'] as $name=>$resname) {
				$resname = strtolower($resname);
				
				if ($row->offsetExists($resname)) {
					$out[ $name ] = $row[ $resname ];
				}
			}
			
		}
		
		return $out;
	}
	
	/**
	 * 
	 * @param IcingaApiResult $result
	 * @return ArrayObject
	 */
	private function rewriteResultRow(IcingaApiResult $result) {
		$row = new ArrayObject($result->getRow());
		$out = new ArrayObject();
		
		foreach ($this->getTemplate()->getFields() as $key=>$field) {
			
			$meta = $this->getTemplate()->getFieldByName($key, 'display');
			$data = $this->getFieldData($row, $key);
			
			$out[$key] = $data;
			
		}
		
		// Adding additional fields
		$out = array_merge((array)$out, (array)$this->addAdditionalFieldResults($result));
		
		unset($row);

		$raw = (array)$out;		
		foreach ($out as $key=>$val) {
			$meta = $this->getTemplate()->getFieldByName($key, 'display');
			if (($param = $meta->getParameter('userFunc')) || ($param = $meta->getParameter('phpFunc'))) {
				if ($param['class'] && $param['method']) {
					if (!array_key_exists('arguments', $param) && !isset($param['arguments'])) $param['arguments'] = array();
					$out[$key] = $this->rewritePerClassMethod($param['class'], $param['method'], $val, $param['arguments'], (array)$raw);
				}
			}
		}
		
		return $out;
	}
	
	private function getFieldData(ArrayObject &$row, $field) {
		$datasource = $this->getTemplate()->getFieldByName($field, 'datasource');
		if ($datasource->getParameter('field')) {
			if (array_key_exists( strtolower( $datasource->getParameter('field') ), $row )) {
				return $row[ strtolower( $datasource->getParameter('field') ) ];
			} 
		}
		
		return null;
	}
	
	private function rewritePerClassMethod($class, $method, $data_val, array $params = array (), array $row = array()) {
		$ref = new ReflectionClass($class);
		if ($ref->isSubclassOf('IcingaTemplateDisplay') && $ref->hasMethod('getInstance') && $ref->hasMethod($method)) {
			$minstance = $ref->getMethod('getInstance');
			$obj = $minstance->invoke(null);
			if ($obj instanceof IcingaTemplateDisplay) {
				$change = $ref->getMethod($method);
				return $change->invoke($obj, 
					$data_val, new AgaviParameterHolder($params), new AgaviParameterHolder($row)
				);
			}
		}
	}
	
	private function getApiField($field_name) {
		return $this->getTemplate()->getFieldByName($field_name, 'datasource')->getParameter('field');
	}
	
	private function buildDataSource() {
		if ($this->api_search === null) {
			$params = $this->getTemplate()->getSectionParams('datasource');
			
			// The wonderfull api
			$search = $this->getApi()->createSearch();
			
			// our query target
			$search->setSearchTarget( AppKit::getConstant($params->getParameter('target')) );
			
			// setting the orders
			
			// Order by
			
			// Overwrite default orders
			foreach ($this->collectOrders() as $order) {
				$search->setSearchOrder($order[0], $order[1]);
			}
			
			// Restrictions
			if (is_array($this->conditions) && count($this->conditions) > 0) {
				foreach ($this->conditions as $condition) {
					$search->setSearchFilter($condition['field'], $condition['val'], $condition['op']);
				}
			}
			
			// Groupby fields
			$gbf = $this->getGroupByFields();
			if (is_array($gbf) && count($gbf)>0) {
				$search->setSearchGroup($gbf);
			}
			
			// Clone our count query
			$this->api_count = clone $search;
			
			// the result columns
			$search->setResultColumns($this->collectCollumns());
			
			// limits
			if (is_numeric($this->pager_limit) && is_numeric($this->pager_start)) {
				$search->setSearchLimit($this->pager_start, $this->pager_limit);
			}
			elseif ($params->getParameter('limit', null)) {
				$search->setSearchLimit(0, $params->getParameter('limit'));
			}
			
			$this->api_search =& $search;
		}

		return true;
	}
	
	private function getGroupByFields() {
		static $fields = null;
		
		if ($fields === null) {
			$db = $this->getTemplate()->getSectionParams('datasource');
			$fields = $db->getParameter('groupby', array());
		}
		
		return $fields;
	}
	
	private function collectOrders() {
		$fields = array();
		if (count($this->sort_orders)) {
			foreach ($this->sort_orders as $order) {
				$params = $this->getTemplate()->getFieldByName($order[0], 'order');
				$fields[] = array (
					$params->getParameter('field', $this->getApiField($order[0])),
					$order[1] ? $order[1] : 'ASC'
				);
			}
		}
		else {
			foreach ($this->getTemplate()->getFieldKeys() as $key) {
				$params = $this->getTemplate()->getFieldByName($key, 'order');
				if ($params->getParameter('enabled') && $params->getParameter('default')) {
					$fields[] = array (
						$params->getParameter('field', $this->getApiField($key)),
						$params->getParameter('order', 'ASC')
					);
				}
			}
		}
		
		return $fields;
	}
	
	private function collectCollumns() {
		$fields = array ();
		
		// The regular fields
		foreach ($this->getTemplate()->getFieldKeys() as $key) {
			$fields[ $this->getApiField($key) ] = false;
		}
		
		// Additional fields
		$ds = $this->getTemplate()->getSection('datasource');
		if (isset($ds['additional_fields']) && is_array($ds['additional_fields'])) {
			$fields = array_merge($fields, array_flip($ds['additional_fields']));
		}
		
		return array_keys($fields);
	}
	
	private function getAdditionalFilterFields() {
		static $fields = null;
		
		if ($fields === null) {
			$ds = $this->getTemplate()->getSection('datasource');
			if ($ds['additional_filter_fields'] && is_array($ds['additional_filter_fields'])) {
				$fields = $ds['additional_filter_fields'];
			}
			else {
				$fields = array ();
			}
		}
		
		return $fields;
	}
	
	/**
	 * Add a condition by a defined xml field
	 * @param string $field
	 * @param mixed $val
	 * @param integer $op
	 * @return string the id of the condition (for deletion)
	 */
	public function setCondition($field, $val, $op = null) {
		if ($op === null) $op = AppKitSQLConstants::SQL_OP_IS;
		$id = 'c-'. AppKitRandomUtil::genSimpleId(15);
		
		$filter = $this->getTemplate()->getFieldByName($field, 'filter');
		$database = $this->getTemplate()->getFieldByName($field, 'datasource');
		$new_field = null;
		
		$ff = $this->getAdditionalFilterFields();
		if (array_key_exists($field, $ff) == true) {
			$new_field = $ff[$field];
		}
		elseif ($filter->getParameter('field', null)) {
			$new_field = $filter->getParameter('field');
		} 
		else {
			$new_field = $database->getParameter('field');
		}
		
		if (!$new_field) {
			throw new IcingaTemplateWorkerException('Could not determine the icinga api field');
		}
		
		// Add or replace some asterix within count
		if ($op == AppKitSQLConstants::SQL_OP_CONTAIN) {
			if (strpos($val, '*') === false) {
				$val = $val. '%';
			}
			else {
				$val = str_replace('*', '%', $val);
			}
		}
		
		$new_op = AppKitSQLConstants::getIcingaMatch($op);
		
		if ($new_op == false) {
			throw new IcingaTemplateWorkerException('No existing icinga search match operator found!');
		}
		
		$this->conditions[ $id ] = array (
			'val'	=> $val,
			'field'	=> $new_field,
			'op'	=> $new_op
		);
		
		return $id;
	}
	
}

class IcingaTemplateWorkerException extends AppKitException { }

?>