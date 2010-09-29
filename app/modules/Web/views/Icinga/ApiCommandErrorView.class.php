<?php

class Web_Icinga_ApiCommandSuccessView extends IcingaWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.ApiCommand');
	}

	public function executeJson(AgaviRequestDataHolder $rd)
	{
		return json_encode(array("success"=>false));

	}
}

?>