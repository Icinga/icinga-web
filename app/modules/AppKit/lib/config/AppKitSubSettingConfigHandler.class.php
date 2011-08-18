<?php
/**
* Allows to import configurations in the same structure like the settings.xml
* Config settigns will be available through the AgaviConfig class
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de> 
**/
class AppKitSubSettingConfigHandler extends AgaviXmlConfigHandler
{
	const XML_NAMESPACE = 'http://agavi.org/agavi/config/parts/settings/1.0';
	
    public function execute(AgaviXmlConfigDomDocument $document)
	{
        $data = array();
        $prefix = "org.icinga.";
       
		$document->setDefaultNamespace(self::XML_NAMESPACE, 'settings');
       
		foreach($document->getConfigurationElements() as $cfg) {
         
            foreach($cfg->get('settings') as $setting) {
                $localPrefix = $prefix;
                 
                // let's see if this buddy has a <settings> parent with valuable information
                if($setting->parentNode->localName == 'settings') {
                    if($setting->parentNode->hasAttribute('prefix')) {
                        $localPrefix = $setting->parentNode->getAttribute('prefix');
                    }
                }
                    
                $settingName = $localPrefix . $setting->getAttribute('name');

                if($setting->hasAgaviParameters()) {

                    $data[$settingName] = $setting->getAgaviParameters();
                } else {
                    $data[$settingName] = AgaviToolkit::literalize($setting->getValue());
                }
            }
          
        }
		$code = 'AgaviConfig::fromArray(' . var_export($data, true) . ');';
        return $this->generate($code, $document->documentURI);

    }
}
