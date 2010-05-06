<?php

class AppKit_LoginSuccessView extends AppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		$this->setAttribute('title', 'Login');
	}
}

?>