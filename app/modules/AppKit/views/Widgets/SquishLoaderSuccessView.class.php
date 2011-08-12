<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView {
    
    public function executeJavascript(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            $content = $this->getAttribute('content');

            $content .= 'AppKit.util.Config.add(\'path\', \''. AgaviConfig::get('org.icinga.appkit.web_path'). '\');'. chr(10);
            $content .= 'AppKit.util.Config.add(\'image_path\', \''. AgaviConfig::get('org.icinga.appkit.image_path'). '\');'. chr(10);
            
            $etag = $this->getAttribute("etag",rand());
            
            $this->getResponse()->setHttpHeader('Cache-Control', 'private', true);
            $this->getResponse()->setHttpHeader('Pragma', null, true);
            $this->getResponse()->setHttpHeader('Expires', null, true);
            $this->getResponse()->setHttpHeader('ETag', '"'. $etag. '"', true);

            if ($this->getAttribute('existsOnClient',false)) {
                $this->getResponse()->setHttpStatusCode("304");
                return "";
            }

            return $content;
        }
    }

    public function executeCss(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            $content = $this->getAttribute('content');

            return $content;
        }
    }

}
