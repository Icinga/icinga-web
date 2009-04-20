<?php

class AppKit_IndexSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd); 
		
		$this->setAttribute('title', 'Home');
	}
}

?>