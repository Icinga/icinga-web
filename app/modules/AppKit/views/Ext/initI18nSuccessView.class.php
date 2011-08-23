<?php

class AppKit_Ext_initI18nSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Ext.initI18n');
    }

    public function executeJavascript(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $files = array();
        $tm = $this->getContext()->getTranslationManager();
        $default = $tm->getDefaultDomain();
        $defaults = explode('.', $default);
        
        $this->loadDefaultFiles($tm, $files);
        $this->loadModuleFiles($tm, $files);
        $this->setAttribute('files', $files);
        $this->setAttribute('default', $defaults[1]);
    }
    
    private function loadModuleFiles($tm,&$files) {
        $default = $tm->getDefaultDomain();

        $translator = $tm->getDomainTranslator($default, AgaviTranslationManager::MESSAGE);

        $locale = $tm->getCurrentLocale();
        $domains = array();
        if ($translator instanceof AppKitGettextTranslator) {
            $basePath = $translator->getDomainPathPattern();
            
            $modules = scandir(AgaviToolkit::literalize("%core.module_dir%"));
            foreach($modules as $m) {
                if($m != '.' && $m != '..')
                    $domains[] = $m;
            }
            foreach($domains as $domain) {
                $path = AgaviToolkit::expandVariables($basePath, array('domain' => $domain));
                foreach(AgaviLocale::getLookupPath($tm->getCurrentLocale()->getIdentifier()) as $prefix) {
                    $result = $this->loadFile($path, $prefix,$files);
                    if($result)
                        $files[$domain] = $result;
                }
            }
        }
        
    }
    
    private function loadDefaultFiles($tm,&$files) {      
        $default = $tm->getDefaultDomain();
        $translator = $tm->getDomainTranslator($default, AgaviTranslationManager::MESSAGE);
        $locale = $tm->getCurrentLocale();
        if ($translator instanceof AppKitGettextTranslator) {
            foreach($translator->getDomainPaths() as $domain=>$path) {
                foreach(AgaviLocale::getLookupPath($tm->getCurrentLocale()->getIdentifier()) as $prefix) {
                    $result = $this->loadFile($path, $prefix);
                    if($result)
                        $files[$domain] = $result;
                }
            }
        }

        
    }
    
    private function loadFile($path,$prefix) {
        $file = sprintf('%s/%s.json', $path, $prefix);

        if (file_exists($file)) {
            $json = file_get_contents($file);

            if (strlen($json)) {
                $sr = array('@\s*\/\*.+?\*\/@s', '@\s*\/\/.+$@m', '@$\s+@m');
                $json = preg_replace($sr, '', $json);
            } else {
                $json = '{}';
            }

            return array($prefix, $json);        
        }
        
    }
}
    
?>