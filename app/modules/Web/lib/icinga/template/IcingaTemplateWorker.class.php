<?php 

class IcingaTemplateWorker {
	
	/**
	 * @var IcingaTemplateXmlParser
	 */
	private $template	= null;
	
	/**
	 * @var IcingaApiConnectionIdo
	 */
	private $api		= null;
	
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
	
	private function getApiField($field_name) {
		return $this->getTemplate()->getFieldByName($field_name, 'datasource')->getParameter('field');
	}
	
	private function buildDataSource() {
		$params = $this->getTemplate()->getSectionParams('datasource');
		
		// The wonderfull api
		$search = $this->getApi()->createSearch();
		
		// our query target
		$search->setSearchTarget( AppKit::getConstant($params->getParameter('target')) );
		
		// the result columns
		$search->setResultColumns($this->collectCollumns());
		
		// setting the orders
		foreach ($this->collectOrders() as $sortfield=>$sortorder) {
			$search->setSearchOrder($sortfield, $sortorder);
		}
		
		// ...
		
		$result = $search->fetch();
		
		foreach ($result as $row) {
			AppKit::debugOut($row->getRow());
		}
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

?>