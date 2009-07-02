<?php

class Web_Icinga_HostDetailSuccessView extends ICINGAWebBaseView
{
	
	private $fields = array(
		'HOST_NAME', 'HOST_ALIAS', 'HOST_CURRENT_STATE', 'HOST_LAST_CHECK', 'HOST_LAST_HARD_STATE_CHANGE',
		'HOST_LAST_STATE_CHANGE', 'HOST_OUTPUT'
	);
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$data = AppKitFactories::getInstance()->getFactory('IcingaData');
		
		$result = $data->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST)
			->setResultColumns($this->fields)
			->fetch();
		
		$this->setAttributeByRef('result', $result);
		
		$this->setAttribute('title', 'Icinga.HostDetail');
	}
}

?>