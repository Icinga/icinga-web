<?php

class Web_Icinga_ServiceDetailSuccessView extends ICINGAWebBaseView
{
	
	private $fields = array(
		'HOST_NAME', 'SERVICE_NAME', 'SERVICE_CURRENT_STATE', 'SERVICE_OUTPUT', 'SERVICE_LAST_STATE_CHANGE',
		'SERVICE_LAST_CHECK'
	);
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$data = AppKitFactories::getInstance()->getFactory('IcingaData');
		
		$result = $data->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_SERVICE)
			->setResultColumns($this->fields)
			->setSearchOrder(array('HOST_NAME', 'SERVICE_NAME'))
			->fetch();
			
		$this->setAttributeByRef('result', $result);
			
		$this->setAttribute('title', 'Icinga.ServiceDetail');
	}
}

?>