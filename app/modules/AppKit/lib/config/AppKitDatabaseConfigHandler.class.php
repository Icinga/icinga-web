<?php

class AppKitDatabaseConfigHandler extends AgaviDatabaseConfigHandler {
    
    const XML_NAMESPACE = 'http://agavi.org/agavi/config/parts/databases/1.0';
    
    public function execute(AgaviXmlConfigDomDocument $document) {
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'database');
        
        $entry_xpath_query = '//ae:configurations/ae:configuration/database:databases';
        
        AppKitXmlUtil::includeXmlFilesToTarget(
            $document,
            $entry_xpath_query,
            'xmlns(ae=http://agavi.org/agavi/config/global/envelope/1.0) xmlns(d=http://agavi.org/agavi/config/parts/databases/1.0) xpointer(//ae:configurations/ae:configuration/d:databases/node())',
            AppKitModuleUtil::getInstance()->getSubConfig('agavi.include_xml.databases')
        );
        
        $document->xinclude();
        
        return parent::execute($document);
    }
    
}

?>