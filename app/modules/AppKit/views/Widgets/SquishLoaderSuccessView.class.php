<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView {
    
    public function executeJavascript(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
            
            $content = $this->getAttribute('content');
            $this->copyConfigToJavascript($content);
            
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
    
    /**
     * Mapping configuration items from AgaviConfig to JS AppKit.util.Config
     * @param string $content 
     */
    private function copyConfigToJavascript(&$content) {
        $map = AgaviConfig::get('modules.appkit.js_config_mapping', array ());
        if (count($map)) {
            
            foreach ($map as $target=>$source) {
                $val = AgaviConfig::get($source ? $source : $target, null);
                $content .= 'AppKit.util.Config.add('. json_encode($target). ', '. json_encode($val). ');'. chr(10);
            }
            
        }
    }

}
