<?php

class AppKit_LogoutSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('title', 'Logout');


        $this->getResponse()->setRedirect(AgaviConfig::get('org.icinga.appkit.logout_path'));
    }
}

?>
