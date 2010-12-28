<?php

class Cronks_Provider_CronksSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Provider.Cronks');
	}

	public function executeJson(AgaviRequestDataHolder $rd) {
		
		if ($this->hasAttribute('cronks')) {
			$json = new AppKitExtJsonDocument();
			$json->hasField('cronkid');
			$json->hasField('module');
			$json->hasField('action');
			$json->hasField('hide');
			$json->hasField('description');
			$json->hasField('name');
			$json->hasField('categories');
			$json->hasField('image');
			$json->hasField('disabled');
			$json->hasField('groupsonly');
			$json->hasField('state');
			$json->setData($this->getAttribute('cronks'));
			return $json->getJson();
		}
		
		return "ERROR";
	}
}

?>