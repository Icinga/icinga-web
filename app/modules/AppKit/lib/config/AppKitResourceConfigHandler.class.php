<?php

/**
 * AppKitResourceConfigHandler allows you to define resource files like javascript and stylesheets which will be sent to the client
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
class AppKitResourceConfigHandler extends AgaviXmlConfigHandler {

    /**
     * Documents' xml namespace
     * @var const string
     */
    const XML_NAMESPACE = 'http://icinga.org/icinga/config/global/resource/1.0';

    /**
     * Resource types to collect
     * @var array string type => string suffix
     */
    protected $_resources = array('javascript' => '.js', 'stylesheets' => '.css');

    /**
     * Collect resource files
     *
     * @param string resource
     * @param string resource suffix
     * @param AgaviXmlConfigDomElement resource config
     *
     * @return array files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    protected function collectResource($resource, $sfx, AgaviXmlConfigDomElement $cfg) {
        $resources = array();

        foreach($cfg->get($resource) as $rcfg) {
            $_resources = array();

            foreach($rcfg->getAgaviParameters() as $r) {
                if(is_dir($r)) {
                    $_resources = array_merge(
                        $_resources,
                        iterator_to_array(
                            AppKitIteratorUtil::RegexRecursiveDirectoryIterator(
                                $r,
                                sprintf('/\%s/i', $sfx)
                            )
                        )
                    );
                } else {
                    $_resources[] = $r;
                }
            }

            $resources = array_merge($resources, $_resources);
        }

        return $resources;
    }

    /**
     * (non-PHPdoc)
     * @see AgaviIXmlConfigHandler::execute()
     */
    public function execute(AgaviXmlConfigDomDocument $doc) {
        $resources = array_combine(
            array_keys($this->_resources),
            array_fill(0, count($this->_resources), array())
        );

        foreach($doc->getConfigurationElements() as $cfg) {
            foreach($this->_resources as $resource => $sfx) {
                $resources[$resource] = array_merge(
                    $resources[$resource],
                    $this->collectResource($resource, $sfx, $cfg)
                );
            }
        }

        return $this->generate(sprintf(
            '$this->resources = array_merge_recursive($this->resources, %s);',
            var_export($resources, true)
        ), $doc->documentURI);
    }

}
