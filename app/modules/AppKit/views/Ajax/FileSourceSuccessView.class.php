<?php

class AppKit_Ajax_FileSourceSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Ajax.FileSource');
	}
	
	public function executeAjax(AgaviRequestDataHolder $rd)
	{
		return $this->getAttribute('content');
	}
}

?>