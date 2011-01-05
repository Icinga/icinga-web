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
			$json->setAttribute(AppKitExtJsonDocument::ATTR_AUTODISCOVER);
			$json->setMeta(AppKitExtJsonDocument::PROPERTY_ID, 'catid');
			$json->setData($categories);
			$json->setSuccess(true);
			
			return $json->getJson();
		}
	}
}

?>