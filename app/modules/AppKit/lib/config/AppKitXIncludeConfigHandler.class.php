<?php

/**
 * AppKitXIncludeConfigHandler includes module configuration files into their global equivalent respectively
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
class AppKitXIncludeConfigHandler extends AgaviXmlConfigHandler {

    /**
     * AgaviConfig format string identifying includes
     * @var string
     *
     * @param string module
     * @param string include namespace
     */
    const INCLUDE_FMT = 'modules.%s.agavi.include.%s';

    /**
     * (non-PHPdoc)
     * @see AgaviIXmlConfigHandler::execute()
     */
    public function execute(AgaviXmlConfigDomDocument $document) {
        $config = basename($document->baseURI, '.xml');

        // For Agavi configuration files we call their appropriate handlers
        // else we hopefully have a handler defined
        if(false == ($configHandlerClass = $this->getParameter('handler', false))) {
            switch($config) {
                case 'databases':
                    $config = 'database';
                    break;
            }

            $configHandlerClass = sprintf('Agavi%sConfigHandler', ucfirst($config));
        }

        if(!class_exists($configHandlerClass)) {
            throw new AgaviConfigurationException("$configHandlerClass not found.");
        }

        $configHandler = new $configHandlerClass($this->getParameters());
        $configHandler->initialize(
            AgaviContext::getInstance($this->context),
            $this->getParameters()
        );

        $nsprefix = array();
        preg_match('/\/([a-z]+)\/[^\/]+$/', $configHandler::XML_NAMESPACE, $nsprefix);
        $nsprefix = $nsprefix[1];

        $document->setDefaultNamespace($configHandler::XML_NAMESPACE, $nsprefix);

        // Order of includes is essential because of dependencies
        $modules = array_merge(
            array('AppKit'),
            AgaviConfig::get('org.icinga.appkit.init_modules')
        );

        foreach($modules as $module) {
            $includes = AgaviConfig::get(
                sprintf(
                    self::INCLUDE_FMT,
                    strtolower($module),
                    $this->getParameter('includeNS', $config)
                ),
                false
            );

            if($includes) {
                $pointers = $this->getParameter('pointer');
                if(!is_array($pointers)) {
                    $pointers = array($pointers);
                }

                foreach($pointers as $pointer) {
                    AppKitXmlUtil::includeXmlFilesToTarget(
                        $document,
                        $this->getParameter('query'),
                        $pointer,
                        $includes
                    );

                    try {
                        $document->xinclude();
                    } catch(Exception $e) {

                    }
                }
            }
        }

        return $configHandler->execute($document);
    }

}
