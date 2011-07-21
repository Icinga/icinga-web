<?php

/**
 * Resource files container
 * @author Eric Lippmann <eric.lippmann@netways.de>
 * @since 1.5.0
 */
class AppKit_ResourcesModel extends AppKitBaseModel implements AgaviISingletonModel {

    /**
     * Collected resources
     * @var array string type => array files
     */
    protected $resources = array();

    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $ctx, array $parameters = array()) {
        parent::initialize($ctx, $parameters);

        include AgaviConfigCache::checkConfig(
            AgaviConfig::get('core.config_dir') . '/javascript.xml'
        );
        include AgaviConfigCache::checkConfig(
            AgaviConfig::get('core.config_dir') . '/stylesheets.xml'
        );
    }

    /**
     * Get javascript files
     *
     * @return array javascript files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function getJavascriptFiles() {
        return $this->resources['javascript'];
    }

    /**
     * Get stylesheet files
     *
     * @return array stylesheet files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function getStylesheetFiles() {
        return $this->resources['stylesheets'];
    }

}
