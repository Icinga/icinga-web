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
	private $api_count		= null;
	
	private $pager_limit	= null;
	private $pager_start	= null;
	
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
	
	public function countResults() {
		if ($this->api_count !== null) {
			$this->api_count->setSearchType(IcingaApi::SEARCH_TYPE_COUNT);
			
			$params = $this->getTemplate()->getSectionParams('datasource');
			
			if (is_array(($fields = $params->getParameter('countfields'))) && count($fields)) {
				$this->api_count->setResultColumns($fields);
			}
			else {
				throw new IcingaTemplateWorkerException('Countfields are empty!');
			}
			
			$result  = $this->api_count->fetch();
			
			return $result->getRow()->count;
		}
		
		return 0;
	}
	
	public function setResultLimit($start, $limit) {
		$this->pager_limit = $limit;
		$this->pager_start = $start;
		return true;
	}
	
	private function getDataAsArray() {
		if ($this->api_search !== null) {
			$data = array ();
			foreach ($this->api_search->fetch() as $result) {
				$data[] = $this->rewriteResultRow($result);
			}
			return $data;
		}
	}
	
	private function rewriteResultRow(IcingaApiResult $result) {
		$row = new ArrayObject($result->getRow());
		
		foreach ($row as $key=>$val) {
			$field = $this->getTemplate()->getFieldByName($key, 'display');
			
			if (($param = $field->getParameter('userFunc'))) {
				if ($param['class'] && $param['method']) {
					$row[$key] = $this->rewritePerClassMethod($param['class'], $param['method'], $val, $param['arguments']);
				}
			}
		}
		
		
		return $row;
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
			foreach ($this->collectOrders() as $sortfield=>$sortorder) {
				$search->setSearchOrder($sortfield, $sortorder);
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
		$orders = array ();
		foreach ($this->getTemplate()->getFieldKeys() as $key) {
			$params = $this->getTemplate()->getFieldByName($key, 'order');
			if ($params->getParameter('enabled') && $params->getParameter('default')) {
				$orders[ $params->getParameter('field', $this->getApiField($key)) ] = 
					$params->getParameter('order', 'ASC');
			}
		}
		return $orders;
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