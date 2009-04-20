<?php

class AppKit_Widgets_ShowFooterSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Widgets.ShowFooter');
	}
}

?>