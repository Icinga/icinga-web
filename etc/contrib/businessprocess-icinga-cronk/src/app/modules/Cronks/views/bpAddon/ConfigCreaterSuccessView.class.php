<?php

class Cronks_bpAddon_ConfigCreaterSuccessView extends CronksBaseView
{

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'bpAddon.ConfigCreater');
	}
}

?>