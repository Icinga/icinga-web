<?php

class Config_Provider_ConfigurationSuccessView extends IcingaConfigBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('_title', 'Provider.Configuration');
    }
    
    public function executeJson(AgaviRequestDataHolder $rd) {
        $data = $this->getAttribute('values', array());
        $doc = new AppKitExtJsonDocument();
        $doc->hasField('key');
        $doc->hasField('value');
        $doc->setData($data);
        $doc->setSuccess(true);
        return $doc->getJson();
    }
}

?>