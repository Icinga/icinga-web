<?php

class Web_Icinga_PortalViewSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
//		$this->setupHtml($rd);
//
//		$this->setAttribute('_title', 'Icinga.PortalView');

		return $this->createForwardContainer('Cronks', 'System.CronkPortal');
	}
}

?>