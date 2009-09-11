<?php

class Cronks_System_ViewProc_AjaxGridLayoutSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Templates.AjaxGridLayout');
	}
}

?>