<?php

class AppKit_SecureSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setAttribute('_title', 'Access Denied');

        $this->setupHtml($rd);

        $this->getResponse()->setHttpStatusCode('401');
    }
}

?>
