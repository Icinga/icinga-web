<?php

class AppKit_IndexSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd); 
		
		$this->setAttribute('title', 'Home');
	}
}

?>