<?php

class AppKit_ModuleDisabledSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
      
        $this->getResponse()->setHttpStatusCode('503');
        return json_encode(array(
            "success" => false,
            "message" => "disabled module called"
        ));
    }
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setAttribute('_title', 'Module Disabled');

        $this->setupHtml($rd);

        $this->getResponse()->setHttpStatusCode('503');
    }
}

?>
