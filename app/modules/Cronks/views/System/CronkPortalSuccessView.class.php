<?php

class Cronks_System_CronkPortalSuccessView extends CronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		print_r($rd->getParameters());
		$this->setAttribute('_title', 'Icinga.Cronks.CronkPortal');
	}
}

?>