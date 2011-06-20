<?php

class Api_ApiDataStoreProviderSuccessView extends IcingaApiBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'ApiDataStoreProvider');
	}
}

?>