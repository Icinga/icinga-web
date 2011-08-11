<?php

class AppKit_DispatcherSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        $container = $this->getAttribute("execContainer");
        $container->execute();
        $res =  $container->getResponse()->getContent();

        return $container->getResponse()->getContent();
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Dispatcher');
    }
}

?>
