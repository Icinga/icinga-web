<?php

class Web_Icinga_Cronks_PersistenceProviderSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.PersistenceProvider');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		
	}
}

?>