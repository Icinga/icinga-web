<?php

/**
 * AppKitResourceConfigHandler collects resources like javascript and css files from global and module configs
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
    protected $_resources = array('javascript' => '.js', 'css' => '.css', 'css_import' => '.css');

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

        foreach($cfg->getChildren($resource, null, false) as $rcfg) {
            $_resources = array();

            foreach($rcfg->getAgaviParameters() as $r) {
                if (is_dir($r)) {
                    $_resources = array_merge(
                                      $_resources,
                                      array_keys(iterator_to_array(
                                                     AppKitIteratorUtil::RegexRecursiveDirectoryIterator(
                                                         $r,
                                                         sprintf('/^[^.].+\%s$/i', $sfx)
                                                     )
                                                 ))
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

        $jactions = array();

        foreach($doc->getConfigurationElements() as $cfg) {

            // Collecting resources
            foreach($this->_resources as $resource => $sfx) {
                $resources[$resource] = array_unique(array_merge(
                                            $resources[$resource],
                                            $this->collectResource($resource, $sfx, $cfg)
                                        ));
            }
            // Collecting javascript actions
            foreach($cfg->getChildren('jactions', null, true) as $jaction) {
                $jactions[] = $jaction->getAgaviParameters();
            }

        }

        return $this->generate(sprintf(
                                   '$this->resources = array_merge_recursive($this->resources, %s);%s$this->jactions=array_merge_recursive($this->jactions, %s);',
                                   var_export($resources, true),
                                   chr(10),
                                   var_export($jactions, true)
                               ), $doc->documentURI);
    }

}
