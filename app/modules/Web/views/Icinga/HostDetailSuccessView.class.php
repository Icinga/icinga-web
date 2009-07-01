<?php

class Web_Icinga_HostDetailSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$data = AppKitFactories::getInstance()->getFactory('IcingaData');
		
		$result = $data->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST)
			->setResultColumns(array('HOST_NAME', 'HOST_ALIAS', 'HOST_CURRENT_STATE'))
			->fetch();
		
		$this->setAttributeByRef('result', $result);
		
		$this->setAttribute('title', 'Icinga.HostDetail');
	}
}

?>