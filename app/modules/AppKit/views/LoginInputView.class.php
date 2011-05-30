<?php

class AppKit_LoginInputView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('title', 'Login');
    }
}

?>