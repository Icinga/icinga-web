<?php

class BPAddon_eventRequestDelegateSuccessView extends BPAddonBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'bpAddon.eventRequestDelegate');
	}

	public function executeJson(AgaviRequestDataHolder $rd) {
		$ctx = $this->getContext();

		$container = $this->getContainer()->createExecutionContainer("Web","Icinga.ApiSearch",$rd,"json");
		$response = $container->execute();
		return $response->getContent();
	}

}

?>