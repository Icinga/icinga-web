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
     * Collected agavi actions which produces javascript
     */
    protected $jactions = array();

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
            AgaviConfig::get('core.config_dir') . '/css.xml'
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
     * Get css files
     *
     * @return array css files
     *
     * @author Eric Lippmann <eric.lippmann@netways.de>
     * @since 1.5.0
     */
    public function getCssFiles() {
        return $this->resources['css'];
    }

    /**
    * Get css imports
    *
    * @return array css files
    *
    * @author Eric Lippmann <eric.lippmann@netways.de>
    * @since 1.5.0
    */
    public function getCssImports() {
        return $this->resources['css_import'];
    }

    public function getJavascriptActions() {
        return $this->jactions;
    }

}
