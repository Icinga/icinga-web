<?php

class AppKit_Ext_HeaderSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
	}

	public function executeJavascript(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
	}

}

?>
