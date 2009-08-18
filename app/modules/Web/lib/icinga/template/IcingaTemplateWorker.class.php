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
					$result  = $this->api_count->fetch();
					$this->result_count = $result->getRow()->count;
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
				
				$data[] = $this->rewriteResultRow($result);
			}
			return $data;
		}
	}
	
	private function rewriteResultRow(IcingaApiResult $result) {
		$row = new ArrayObject($result->getRow());
		$out = new ArrayObject();
		foreach ($this->getTemplate()->getFields() as $key=>$field) {
			
			$meta = $this->getTemplate()->getFieldByName($key, 'display');
			$data = $this->getFieldData($row, $key);
			
			// Ommit blank data!
			if (!$meta->getParameter('visible') === true) continue;
			
			if (($param = $meta->getParameter('userFunc'))) {
				if ($param['class'] && $param['method']) {
					if (!is_array($param['arguments'])) $param['arguments'] = array();
					$out[$key] = $this->rewritePerClassMethod($param['class'], $param['method'], $data, $param['arguments']);
				}
			}
			else {
				$out[$key] = $data;
			}
			
		}
		
		unset($row);
		
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
	
	private function rewritePerClassMethod($class, $method, $data_val, array $params = array ()) {
		$ref = new ReflectionClass($class);
		if ($ref->isSubclassOf('IcingaTemplateDisplay') && $ref->hasMethod('getInstance') && $ref->hasMethod($method)) {
			$minstance = $ref->getMethod('getInstance');
			$obj = $minstance->invoke(null);
			if ($obj instanceof IcingaTemplateDisplay) {
				$change = $ref->getMethod($method);
				return $change->invoke($obj, $data_val, new AgaviParameterHolder($params));
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
			
			// Clone our count query
			$this->api_count = clone $search;
			
			// the result columns
			$search->setResultColumns($this->collectCollumns());
			
			// limits
			if (is_numeric($this->pager_limit) && is_numeric($this->pager_start)) {
				$search->setSearchLimit($this->pager_start, $this->pager_limit);
			}
			
			$this->api_search =& $search;
		}

		return true;
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
		
		foreach ($this->getTemplate()->getFieldKeys() as $key) {
			$fields[ $this->getApiField($key) ] = false;
		}
		
		return array_keys($fields);
	}
	
}

class IcingaTemplateWorkerException extends AppKitException { }

?>