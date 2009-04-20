<?php

class AppKit_Ajax_TestSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Ajax.Test');
	}
}

?>