<?php

class AppKit_LogoutSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Logout');
	}
}

?>