<?php

class Web_Icinga_Cronks_CronkPortalSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.CronkPortal');
	}
}

?>