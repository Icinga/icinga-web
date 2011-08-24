<?php

class BPAddon_js_bpLoaderSuccessView extends IcingaBPAddonBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'bpAddon.js.bpLoader');
	}
}

?>