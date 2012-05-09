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

        $refClass = $this->getConfigHandlerClass($config);

        $configHandler = $refClass->newInstance();

        $configHandler->initialize($this->context, $this->parameters);

        $nsprefix = null;

        if ($refClass->hasConstant('XML_NAMESPACE')) {

            $m = array();
            preg_match('/\/([a-z]+)\/[^\/]+$/', $refClass->getConstant('XML_NAMESPACE'), $m);
            $nsprefix = $m[1];

            $document->setDefaultNamespace($refClass->getConstant('XML_NAMESPACE'), $nsprefix);
        } else {
            throw new AgaviConfigurationException('Could not read XML_NAMESPACE from class: '. $refClass->getName());
        }
     
        $modules = $this->getAvailableModules(); 

        $query = $this->getQuery();

        $pointers = $this->getPointers();
        
        foreach($modules as $module) {
            $includes = AgaviConfig::get(
                            sprintf(
                                self::INCLUDE_FMT,
                                strtolower($module),
                                $this->getParameter('includeNS', $config)
                            ),
                            false
                       );
            
            if ($includes) {
                
                if(isset($includes["folder"]))
                    $includes = $this->resolveFolder($includes); 
              
                foreach($pointers as $pointer) {
                    AppKitXmlUtil::includeXmlFilesToTarget(
                        $document,
                        $query,
                        $pointer,
                        $includes
                    );
             
                    try {
                        $document->xinclude();
                    } catch (Exception $e) {
                        
                    }
                   
                }
            }
        }
        
        // The confighandler behind this included definition
        return $configHandler->execute($document);
    }

    private function resolveFolder(array $include) {
        $folder = $include["folder"];
        if(!is_dir($folder)) 
            return array();
        if(!is_readable($folder))
            return array();
        $files = scandir($folder);
        $pattern = "/.*\.xml/";
        if(isset($include["pattern"]))
            $pattern = "/".$include["pattern"]."/";
      
        
        $resultset = array();
        foreach($files as $file) {
            if(preg_match($pattern,$file))
                $resultset[] = $folder."/".$file;
        }
        
        return $resultset;
    }
    

    /**
     * Returns the right config handler
     * @param string $config
     * @throws AgaviConfigurationException
     * @return ReflectionClass
     */
    private function getConfigHandlerClass($config) {
        // For Agavi configuration files we call their appropriate handlers
        // else we hopefully have a handler defined
        if (false == ($configHandlerClass = $this->getParameter('handler', false))) {
            switch ($config) {
                case 'databases':
                    $config = 'database';
                    break;
            }

            $configHandlerClass = sprintf('Agavi%sConfigHandler', ucfirst($config));
        }

        if (!class_exists($configHandlerClass)) {
            throw new AgaviConfigurationException("$configHandlerClass not found.");
        }

        $ref = new ReflectionClass($configHandlerClass);
        return $ref;
    }

    /**
     * Returns the XPath query and substitude some vars needed there
     * @return string
     */
    private function getQuery() {
        $query = $this->getParameter('query');

        // Allow different contexts in xpath queries and
        // substitude the variable
        if (strpos($query, '__CONTEXT__') !== false) {
            $query = str_replace('__CONTEXT__', AgaviConfig::get('core.default_context', 'web'), $query);
        }

        return $query;
    }

    private function getAvailableModules() {
        $modules = array('AppKit');
        $module_dir = AgaviToolKit::literalize("%core.module_dir%");
        $files = scandir($module_dir);
       
        foreach($files as $file) {
            if($file == '.' || $file == '..' || $file == 'AppKit' 
                || $file == 'Config') {
                continue;
            }
            
            if(!is_dir($module_dir."/".$file)
                || !is_readable($module_dir."/".$file)) {
                continue;
            }
            
            $modules[] = $file;
        }
        
        $modules[] = 'Config';
        
        return $modules;
    }

    /**
     * Returns an array of xpointer to include from
     * @return array
     */
    private function getPointers() {
        $pointers = $this->getParameter('pointer');

        if (!is_array($pointers)) {
            $pointers = array($pointers);
        }
        foreach($pointers as &$pointer) {
            if (strpos($pointer, '__CONTEXT__') !== false) {
                $pointer = str_replace('__CONTEXT__', AgaviConfig::get('core.default_context', 'web'), $pointer);
            }
        }
        return $pointers;
    }

}
