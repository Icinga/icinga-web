<?php

class Cronks_Provider_ObjectInfoIconsSuccessView extends CronksBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$this->setAttribute('_title', 'Provider.ObjectInfoIcons');
	}
    
    public function executeJson(AgaviRequestDataHolder $rd) {
        
        $out = array (
            'success' => false,
            'count' => 0,
            'rows' => array ()
        );
        
        if (is_array(($data = $this->getAttribute('info_data', null)))) {
            $out['success'] = true;
            $out['count'] = count($data);
            $out['rows'] = $data;
        }
        
        return json_encode($out);
    }
}