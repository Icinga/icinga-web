<?php

class BPAddon_OverviewSuccessView extends BPAddonBaseView
{

	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		$this->setAttribute("url",$rd->getParameter('p[url]'));
		$name = $rd->getParameter('p[name]');
		$pass = $rd->getParameter('p[pass]');
		$authToken = "Basic ".base64_encode($name.":".$pass);
		$this->setAttribute("authToken",$authToken);
		$this->setAttribute('_title', 'bpAddon.Overview');
	}
}

?>