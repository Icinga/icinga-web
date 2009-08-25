<?php

class Web_Icinga_ViewTestSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		$this->setAttribute('title', 'Icinga.Templates.ViewTest');
	}
}

?>