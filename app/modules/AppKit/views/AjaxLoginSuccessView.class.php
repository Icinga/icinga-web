<?php

class AppKit_AjaxLoginSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'AjaxLogin');
	}
}

?>