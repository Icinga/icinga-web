<?php

class Web_Icinga_Templates_AjaxGridLayoutSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Templates.AjaxGridLayout');
	}
}

?>