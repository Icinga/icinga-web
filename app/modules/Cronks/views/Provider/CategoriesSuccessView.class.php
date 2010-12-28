<?php

class Cronks_Provider_CategoriesSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Provider.Categories');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		if ($this->hasAttribute('categories')) {
			$categories = $this->getAttribute('categories', array());

			$json = new AppKitExtJsonDocument();
			$json->hasField('catid');
			$json->hasField('title');
			$json->hasField('visible');
			$json->hasField('active');
			$json->hasField('position');
			$json->setData($categories);
			$json->setSuccess(true);
			
			return $json->getJson();
		}
	}
}

?>