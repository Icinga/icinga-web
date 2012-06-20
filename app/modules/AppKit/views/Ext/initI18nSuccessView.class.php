<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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