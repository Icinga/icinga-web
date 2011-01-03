<?php

class Cronks_Provider_CronksSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('_title', 'Provider.Cronks');
	}

	public function executeJson(AgaviRequestDataHolder $rd) {
		if ($this->hasAttribute('errors')) {
			$this->getContainer()->getResponse()->setHttpStatusCode(500);
			return json_encode(array('errors' => $this->getAttribute('errors')));
		}
		
		if ($rd->getParameter('xaction') == 'write') {
			$return = array (
				'success' => true,
				'errors' => new stdClass()
			);
			
			return json_encode($return);
		}
		
		if ($this->hasAttribute('cronks')) {
			$json = new AppKitExtJsonDocument();
			
			$json->setAttribute(AppKitExtJsonDocument::ATTR_AUTODISCOVER);
			
			$json->setData($this->getAttribute('cronks'));
			
			return $json->getJson();
		}
	}
}

?>