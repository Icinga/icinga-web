<?php

class AppKit_Admin_Groups_RemoveSuccessView extends ICINGAAppKitBaseView
{
	public function executeSimplecontent(AgaviRequestDataHolder $rd) {
		return null;
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Admin.Groups.Remove');
	}
}

?>