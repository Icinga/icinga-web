<?php

class AppKit_SecureSuccessView extends AppKitBaseView {

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
    }

    private function sendHeader() {
        $this->getResponse()->setHttpStatusCode('401');
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->sendHeader();
        $this->setAttribute('_title', 'Access Denied');
        $this->setupHtml($rd);
    }


    public function executeJson(AgaviRequestDataHolder $rd) {
        return json_encode(array(
            'success' => false,
            'errors' => array('401 unauthorized')
        ));
    }
}

?>
