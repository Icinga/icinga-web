<?php

class AppKit_User_IndexSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'User preferences');
	}
}

?>