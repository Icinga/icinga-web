<?php

class AppKit_DispatcherErrorView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        $errors = $this->getContainer()->getValidationManager()->getErrorMessages();

        if (empty($errors)) {
            $errors = $this->getAttribute("error");
        }

        $err = array("success"=>false,"error"=> $errors);
        return json_encode($err);
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Dispatcher');
    }
}

?>
