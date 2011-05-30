<?php

class AppKit_Privileges_IndexSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Privileges.Index');
    }
}

?>