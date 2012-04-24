<?php

class Config_ShowConfigurationSuccessView extends IcingaConfigBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'ShowConfiguration');
	}
}

?>