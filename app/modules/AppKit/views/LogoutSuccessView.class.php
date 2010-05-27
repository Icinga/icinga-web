<?php

class AppKit_LogoutSuccessView extends AppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Logout');
		
		
		$this->getResponse()->setRedirect(AgaviConfig::get('de.icinga.appkit.web_path'));
	}
}

?>