<?php

class AppKit_UnavailableSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setAttribute('_title', 'Application Unavailable');

        $this->setupHtml($rd);

        $this->getResponse()->setHttpStatusCode('503');
    }
}

?>