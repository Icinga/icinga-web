<?php

class AppKit_DataProvider_LanguageProviderSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        try {
            $context = $this->getContext();
            $tm = $context->getTranslationManager();
            $locales = $tm->getAvailableLocales();
            $localeList = array();
            foreach($locales as $locale) {
                $id = $locale["identifier"];
                $localeList[] = array(
                                    "id"=> $id,
                                    "description" => $locale["parameters"]["description"],
                                    "isCurrent" => $id = $tm->getCurrentLocaleIdentifier()
                                );
            }
            return json_encode(array("success"=>true,"locales"=>$localeList));

        } catch (Exception $e) {
            $this->getResponse()->setHttpStatusCode(500);
            return json_encode(array("errorMessage" => "An exception occured: ".$e->getMessage(),"isBug"=>true));
        }
    }
}

?>