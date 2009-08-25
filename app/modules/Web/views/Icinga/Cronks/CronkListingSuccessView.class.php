<?php

class Web_Icinga_Cronks_CronkListingSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.CronkListing');
	}
}

?>