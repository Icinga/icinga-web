<?php

class AppKit_Error404SuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setAttribute('_title', '404 Not Found');
		
		$this->setupHtml($rd);

		$this->getResponse()->setHttpStatusCode('404');
	}
}

?>