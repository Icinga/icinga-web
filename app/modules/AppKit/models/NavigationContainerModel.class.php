<?php

/**
 * Checking menu entries for specific conditions (e.g. credentials)
 * @author jmosshammer
 *
 */
class AppKit_NavigationContainerModel extends AppKitBaseModel {
    /**
     * @var AgaviWebRouting
     */
    private $ro = null;
    private $config = null;
    private $user;

    /**
     * (non-PHPdoc)
     * @see AppKitBaseModel::initialize()
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->user = $context->getUser();
    }

    /**
     * Returns the menu tree as json
     * @return string
     */
    public function getJsonData() {

        return json_encode($this->createMenuDescriptor());
    }

    public function readJsonFromConfig() {
        $config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.config_dir%/menu.xml'));
        $menu = $this->getAllowedMenuPoints($config);

        return $menu;
    }

    private function getAllowedMenuPoints(array $config) {
        $m = array();
        foreach($config as $elem) {
            if (isset($elem["credential"])) {
                $credentials = explode(";",$elem["credential"]);

                if (!$this->hasCredentials($credentials)) {
                    continue;
                }
            }

            if (isset($elem['items'])) {
                $items = $this->getAllowedMenuPoints($elem['items']);
                $elem['items'] = $items;
            }

            $m[] = $elem;
        }
        return $m;
    }
    private function hasCredentials(array $credentials) {
        foreach($credentials as $credential) {
            if (!$this->user->hasCredential($credential)) {
                return false;
            }
        }
        return true;
    }

    private function createMenuDescriptor() {
        $cfg = $this->readJsonFromConfig();


        return $cfg;
    }


}
