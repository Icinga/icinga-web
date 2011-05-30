<?php

class AppKit_LogoutInputView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('title', 'Logout');
    }
}

?>