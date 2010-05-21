<?php

class Web_IndexSuccessView extends IcingaWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Welcome to icinga-web');
	}
}

?>