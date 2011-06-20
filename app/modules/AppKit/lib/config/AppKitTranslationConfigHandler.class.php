<?php
/**
 * This file holds the class AppKitTranslationConfigHandler and additional
 * ressourcen to extending translations on-the-fly
 */

/**
 * This class add on-the-fly extension for agavi translation settings. We're using
 * the module register mechanism to detect additional module-based translation
 * resourcen and add them with XInclude to the main configuration
 * @author mhein <marius.hein@netways.de>
 * @package icinga-web
 * @subpackage appkit
 * @since 1.5
 */
class AppKitTranslationConfigHandler extends AgaviTranslationConfigHandler {
    
    /**
     * (non-PHPdoc)
     * @see AgaviTranslationConfigHandler::execute()
     */
    public function execute(AgaviXmlConfigDomDocument $document) {
        
        $document->setDefaultNamespace(parent::XML_NAMESPACE, 't');
        
        $entry_xpath_query = '//ae:configurations/ae:configuration/t:translators[@default_domain=\'icinga.default\']';
        
        AppKitXmlUtil::includeXmlFilesToTarget(
            $document,
            $entry_xpath_query,
            'xmlns(ae=http://agavi.org/agavi/config/global/envelope/1.0) xmlns(t='. parent::XML_NAMESPACE. ') xpointer(//ae:configurations/ae:configuration/t:translators/t:translator)',
            AppKitModuleUtil::getInstance()->getSubConfig('agavi.include_xml.translations')
        );
        
        $document->xinclude();
        
        return parent::execute($document);
    }
    
}

?>