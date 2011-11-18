<?php

class Api_RelationProviderSuccessView extends IcingaApiBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		$this->setAttribute('_title', 'ApiObjectInfo');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
	    $data = $this->getAttribute('data', array ());
	    
	    $data['id'] = 1;
	    
	    $out = array (
	        'result' => $data,
	        'success' => false
	    );
	    
	    if (isset($data['object']) && is_array($data['object'])) {
	        $out['success'] = true;
	    }
	    
	    return json_encode($out);
	}
}

?>