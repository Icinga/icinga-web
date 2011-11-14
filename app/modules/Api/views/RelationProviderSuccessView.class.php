<?php

class Api_RelationProviderSuccessView extends IcingaApiBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('_title', 'ApiObjectInfo');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    var_dump($this->getAttribute('data', array ()));
	    return json_encode(array('huhu' => true));
	}
}

?>