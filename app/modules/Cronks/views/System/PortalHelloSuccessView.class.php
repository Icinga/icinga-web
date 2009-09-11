<?php

class Cronks_System_PortalHelloSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.PortalHello');
	}
}

?>