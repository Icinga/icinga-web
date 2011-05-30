<?php

class AppKit_Error404SuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setAttribute('_title', '404 Not Found');

        $this->setupHtml($rd);

        $this->getResponse()->setHttpStatusCode('404');
    }

    public function executeConsole(AgaviRequestDataHolder $rd) {
        $val = $this->getContainer()->getValidationManager();
        foreach($val->getErrors() as $error=>$msg) {
            printf("Error: ".$error." ".$msg."\n");
        }

    }
}

?>