<?php

class AppKit_ServertimeSuccessView extends AppKitBaseView
{
	public function executeSimple(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Servertime');
	}
}

?>