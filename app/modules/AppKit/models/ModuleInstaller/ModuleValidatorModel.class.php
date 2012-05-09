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

// Exception tree
class ModuleValidationException extends AppKitException {}
class ModuleFolderDoesNotExistException extends ModuleValidationException {}
class ModuleFolderPermissionException extends ModuleValidationException {}

class AppKit_ModuleInstaller_ModuleValidatorModel extends AppKitBaseModel {
    protected $modulename;
    protected $dir; 
    protected $moduleDom;
    protected $hasAccessXML = false;
    protected $hasDatabasesXML = false;
    protected $hasMenuXML = false;


    public function validateModule($modulename) {
        $this->modulename = $modulename;
        $this->moduleDom  = new AgaviXmlConfigDomDocument();
        $this->checkModuleFilesystemConsistency();
        $this->checkModuleConfig();
        $this->checkVersion();
        $this->checkIncludes();
    }

    protected function checkModuleFilesystemConsistency() {
        $dir = AgaviToolKit::literalize("%core.module_dir/".$this->modulename);
        if(!is_dir($dir))
            throw new ModuleFolderDoesNotExistException("Module folder for $modulename couldn't be found at ".$dir);
        if(!is_dir($dir."/config"))
            throw new ModuleFolderDoesNotExistException("Config folder for $modulename couldn't be found at ".$dir);
        if(!is_readable($dir."/config/module.xml") && !is_readable($dir."/config/module.xml.in"))
            throw new ModuleFolderPermissionException("Couldn't read module.xml (or module.xml.in)");

        $this->dir = $dir;
 
        // get properties of this module
        if(is_readable($dir."/config/menu.xml"))
            $this->hasMenuXML = true;
        if(is_readable($dir."/config/access.xml"))
            $this->hasAccessXML = true;
        if(is_readable($dir."/config/databases.xml"))
            $this->hasDatabasesXML = true;
    }       
    

    protected function checkModuleConfig() {

    }

    protected function checkVersion() {

    }
    
}
