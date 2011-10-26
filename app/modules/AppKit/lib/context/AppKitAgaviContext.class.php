<?php
/**
 * The replacement agavi context to handle all bootstrap things
 * in that
 */
class AppKitAgaviContext extends AgaviContext {
    
    private static $excludeModules = array (
        'AppKit', 'TestDummy'
    );
    
    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/core/AgaviContext#initialize()
     */
    public function initialize() {

        // Fill in the context
        AppKitAgaviUtil::initContext($this);

        /*
         * Make our settings ready
         * before run agavi
         */
        $this->buildVersionString();
        $this->initializePhpSettings();

        $this->initializeModules();

        parent::initialize();

        $this->initializeAutosettings();

        $this->initializeExceptionHandling();
    }

    /**
     * Our own exception handler created here
     */
    private function initializeExceptionHandling() {
        AppKitExceptionHandler::initializeHandler();
    }

    /**
     * Load all needed modules
     */
    private function initializeModules() {
        $list = array();
        $module_dir = AgaviToolKit::literalize("%core.module_dir%");
        $files = scandir($module_dir);
       
        AppKitAgaviUtil::initializeModule('AppKit');
        foreach($files as $file) {
            if($file == '.' || $file == '..')
                continue;
            if(!is_dir($module_dir."/".$file) || !is_readable($module_dir."/".$file))
                continue;
            $list[] = $file;
        }

        foreach($list as $mod_name) {
            try {
               if(in_array($mod_name, self::$excludeModules) === false) {
                    AppKitAgaviUtil::initializeModule($mod_name);
               }
            } catch(AgaviDisabledModuleException $e) {
            
            }
        }
    }


    private function initializeAutosettings() {
        // Try to set the web path to correct urls within the frontend
        if (AgaviConfig::get('core.default_context') =='web') {
            // Try to set the web path to correct urls within the frontend
            if (AgaviConfig::get('org.icinga.appkit.web_path', null) == null) {
                AgaviConfig::set('org.icinga.appkit.web_path', AppKitStringUtil::extractWebPath(), true, true);
            }
        }

        include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.config_dir%/schedules.xml'));
         
        // Global temp directory
        AgaviConfig::set('core.tmp_dir', AgaviConfig::get('core.data_dir'). '/tmp');
    }

    /**
     * Glue our version string together
     */
    private function buildVersionString() {
        if (AgaviConfig::get('org.icinga.version.extension', false) == false) {
            $version_format = "%s/v%d.%d.%d";
        } else {
            $version_format = "%s/v%d.%d.%d-%s";
        }

        AgaviConfig::set('org.icinga.version.release', sprintf(
                             $version_format,
                             AgaviConfig::get('org.icinga.version.name'),
                             AgaviConfig::get('org.icinga.version.major'),
                             AgaviConfig::get('org.icinga.version.minor'),
                             AgaviConfig::get('org.icinga.version.patch'),
                             AgaviConfig::get('org.icinga.version.extension')
                         ), true, true);
    }

    /**
     * Change PHP settings at runtime
     * @throws AppKitException
     */
    private function initializePhpSettings() {
        // Applying PHP settings
        if (is_array($settings = AgaviConfig::get('modules.appkit.php_settings'))) {
            foreach($settings as $ini_key=>$ini_val) {
                if (ini_set($ini_key, $ini_val) === false) {
                    throw new AppKitException("Could not set ini setting $ini_key to '$ini_val'");
                }
            }
        }
    }

}
