<?php

class Api_ApiSearchErrorView extends IcingaApiBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        return "Invalid Arguments!";
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $context = $this->getContext();
        $validation = $this->getContainer()->getValidationManager();
        $errorMsg = array("error"=>array());
        foreach($validation->getErrorMessages() as $error) {
            $errorMsg["error"][] =  $error;
        }
        return json_encode($errorMsg);
    }

    public function executeXml(AgaviRequestDataHolder $rd) {
        echo "<?xml version='1.0' encoding='utf-8'?>";
        $validation = $this->getContainer()->getValidationManager();
        foreach($validation->getErrorMessages() as $error) {
            echo "<error><message>".$error['message']."</message></error>";
        }
    }

    public function executeSimple(AgaviRequestDataHolder $rd) {
        echo "Invalid arguments";
    }


    public function executeRest(AgaviRequestDataHolder $rd) {
        echo "Invalid arguments";
    }
}
