<?php

class Cronks_bpAddon_js_bpLoaderSuccessView extends IcingaCronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'bpAddon.js.bpLoader');
	}
}

?>