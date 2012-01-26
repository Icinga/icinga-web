<?php

class AppKit_LogoutSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setAttribute('title', 'Logout');
        
        if ($this->getAttribute('sendHeader', false) === true) {
            $this->getResponse()->setHttpStatusCode('401');
        
            
            /*
             * @TODO: Not used at the moment
             */
            
//             if ($this->getAttribute('auth_realm') !== null) {
//                 $this->getResponse()->setHttpHeader(
//                         	'WWW-Authenticate',
//                         	'Basic realm="'. $this->getAttribute('auth_realm'). '"'
//                 );
//             }
        
        }
        
        if ($this->getAttribute('doRedirect') === true) {
            
            $routeName = AgaviConfig::get('org.icinga.appkit.logout_route');
            $url = $this->getContext()->getRouting()->gen($routeName, array(), array('relative' => false));
            
            if ($this->getAttribute('auth_realm') !== null) {
                $url = str_replace('://', '://anonymous@', $url);
            }

            $this->getResponse()->setHttpHeader('Refresh', '0; url='. $url);
        } else {
            $this->setupHtml($rd);
        }
    }
}

?>
