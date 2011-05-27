<?php

class AppKit_Ext_DynamicJavascriptSourceSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $this->setAttribute('_title', 'Ext.DynamicJavascriptSource');
    }

    public function executeJavascript(AgaviRequestDataHolder $rd) {

        $scripts = $this->getContext()->getRequest()->getAttribute('app.javascript_dynamic', AppKitModuleUtil::DEFAULT_NAMESPACE);
        $script = $rd->getParameter('script');

        if(isset($scripts) && array_key_exists($script, $scripts) && file_exists($scripts[$script])) {
            return file_get_contents($scripts[$script]);
        } else {
            throw new AgaviViewException(sprintf('Dynamic script %s was not defined', $script));
        }

        return;
    }
}

?>