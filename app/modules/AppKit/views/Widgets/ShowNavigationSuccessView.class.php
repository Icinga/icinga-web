<?php

class AppKit_Widgets_ShowNavigationSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Widgets.ShowNavigation');
	}
}

?>