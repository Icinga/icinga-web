<?php

class AppKit_PingSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {


        $this->setAttribute('_title', 'Ping');
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Ping');
    }
}

?>
