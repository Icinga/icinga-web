<?php

class Api_ApiDataStoreProviderErrorView extends IcingaApiBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        $errors = $this->getContainer()->getValidationManager()->getErrorMessages();
        print_r($errors);
        return json_encode(array(
                               "success" => "false",
                               "errors" => $errors));
    }
}
