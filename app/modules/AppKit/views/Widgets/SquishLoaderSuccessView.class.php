<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView {
    
    public function executeJavascript(AgaviRequestDataHolder $rd) {
        if ($this->getAttribute('errors', false)) {
            return "throw '". join(", ", $this->getAttribute('errors')). "';";
        } else {
	
            $content = '';    
            $this->copyConfigToJavascript($content);
            $content .= $this->getAttribute('content');
            
            $etag = $this->getAttribute("etag",rand());
            
            $this->getResponse()->setHttpHeader('Cache-Control', 'private', true);
            $this->getResponse()->setHttpHeader('Pragma', null, true);
            $this->getResponse()->setHttpHeader('Expires', null, true);
            $this->getResponse()->setHttpHeader('ETag', '"'. $etag. '"', true);

            if ($this->getAttribute('existsOnClient',false)) {
                $this->getResponse()->setHttpStatusCode("304");
                return "";
            }
            
            ob_start("ob_gzhandler");

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
            $out = 'var Icinga = { AppKit: { configMap: {} }};'. chr(10);
            foreach ($map as $target=>$source) {
                $val = AgaviConfig::get($source ? $source : $target, null);
                $out .= 'Icinga.AppKit.configMap['. json_encode($target). '] = '. json_encode($val). ';'. chr(10);
            }
            $content .= $out;
        }
    }

}
