<?php

class AppKit_Admin_Groups_RemoveSuccessView extends AppKitBaseView {
    public function executeSimple(AgaviRequestDataHolder $rd) {
        return null;
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.Groups.Remove');
    }
}

?>