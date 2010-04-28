<?php

class AppKit_DataProvider_LanguageProviderSuccessView extends ICINGAAppKitBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd)
	{
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
		return json_encode(array("locales"=>$localeList));
	}
}

?>