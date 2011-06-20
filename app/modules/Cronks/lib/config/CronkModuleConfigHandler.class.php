<?php

class CronkModuleConfigHandler extends AgaviXmlConfigHandler {
    
    const XML_NAMESPACE = 'http://icinga.org/cronks/config/parts/cronks/1.0';
    
    public function execute(AgaviXmlConfigDomDocument $document) {
        
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'cronk');
        
        $config = $document->documentURI;
        
        $cronks = array ();
        $categories = array ();
        
        foreach ($document->getConfigurationElements() as $cfg) {
            $this->getXmlConfiguration($cfg, 'cronks', 'cronk', $cronks);
            $this->getXmlConfiguration($cfg, 'categories', 'category', $categories);
        }
        
        $code  = 'return '. var_export(array($cronks, $categories), true);
        
        return $this->generate($code, $config);
    }
    
    private function getXmlConfiguration(AgaviXmlConfigDomElement $cfg, $tuple_element, $item_element, &$store) {
        if ($cfg->has($tuple_element)) {
            foreach ($cfg->get($tuple_element) as $item) {
                if ($item->hasAgaviParameters()) {
                    $store[$item->getAttribute('name')] = $item->getAgaviParameters();
                }
            }
            return true;
        }
    }
    
}

?>