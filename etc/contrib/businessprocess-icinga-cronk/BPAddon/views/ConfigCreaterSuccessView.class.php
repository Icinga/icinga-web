<?php

class BPAddon_ConfigCreaterSuccessView extends BPAddonBaseView
{

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'bpAddon.ConfigCreater');
	}
}

?>