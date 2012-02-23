<?php

class AppKit_LogoutSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        
        $this->setupHtml($rd);
        
        $this->setAttribute('title', 'Logout');
        
        if ($this->getAttribute('sendHeader', false) === true) {
            $this->getResponse()->setHttpStatusCode('401');
        }
        
            
        $routeName = AgaviConfig::get('org.icinga.appkit.logout_route');
        $url = $this->getContext()->getRouting()->gen($routeName, array(), array('relative' => false));
        $this->setAttribute('url', $url);
        
        if ($this->getAttribute('doRedirect') && $this->getAttribute('httpBasic', false) == false) {
            $this->getResponse()->setRedirect($url);
        }
    }
}

?>
