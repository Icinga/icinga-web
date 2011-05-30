<?php

class AppKit_User_IndexSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('title', 'User preferences');
    }
}

?>